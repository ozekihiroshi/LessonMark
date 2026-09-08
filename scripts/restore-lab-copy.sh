#!/bin/sh
# Restore into new internal-only containers, never into the owner's 8095 site.
set -eu
umask 077
root="$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)"
backup="$root/build/upgrade-lab/pre-rc2-20260908T075649Z"
result="$root/build/upgrade-lab/recovery-check"
mkdir -p "$result"
(cd "$backup" && sha256sum -c SHA256SUMS >/dev/null)
. "$backup/runtime.env"
web=lessonmark-recovery-check
db=lessonmark-recovery-check-db
network=lessonmark-recovery-check-internal
for target in "$web" "$db"; do
    if docker container inspect "$target" >/dev/null 2>&1; then
        echo "Existing recovery container found; inspect before retry: $target" >&2
        exit 1
    fi
done
for target in lessonmark-recovery-check-data lessonmark-recovery-check-backups lessonmark-recovery-check-dbdata; do
    if docker volume inspect "$target" >/dev/null 2>&1; then
        echo "Existing recovery volume found; refusing overwrite: $target" >&2
        exit 1
    fi
done
docker network create --internal "$network" >/dev/null
docker run -d --name "$db" --network "$network" \
    -e MARIADB_ROOT_PASSWORD="$UI_TEST_DB_ROOT_PASSWORD" \
    -e MARIADB_DATABASE="$UI_TEST_DB_NAME" -e MARIADB_USER="$UI_TEST_DB_USER" \
    -e MARIADB_PASSWORD="$UI_TEST_DB_PASSWORD" \
    -v lessonmark-recovery-check-dbdata:/var/lib/mysql mariadb:11.8 >/dev/null
attempt=0
until docker exec "$db" healthcheck.sh --connect --innodb_initialized >/dev/null 2>&1; do
    attempt=$((attempt + 1)); test "$attempt" -lt 90; sleep 1
done
docker exec -i "$db" sh -c 'MYSQL_PWD="$MARIADB_ROOT_PASSWORD" mariadb -uroot' < "$backup/database.sql"
image="$(cat "$backup/image-id.txt")"
docker run -d --name "$web" --network "$network" \
    -e MOODLE_DB_HOST="$db" -e MOODLE_DB_NAME="$UI_TEST_DB_NAME" \
    -e MOODLE_DB_USER="$UI_TEST_DB_USER" -e MOODLE_DB_PASSWORD="$UI_TEST_DB_PASSWORD" \
    -e MOODLE_WWWROOT=http://localhost:8095 -e MOODLE_REVERSE_PROXY=false -e MOODLE_SSL_PROXY=false \
    -v lessonmark-recovery-check-data:/var/moodledata \
    -v lessonmark-recovery-check-backups:/var/moodlebackups \
    "$image" sleep infinity >/dev/null
docker cp "$backup/code-config.tgz" "$web:/tmp/recovery-code.tgz"
docker cp "$backup/moodledata-backups.tgz" "$web:/tmp/recovery-data.tgz"
docker exec "$web" tar -xzf /tmp/recovery-code.tgz -C /var/www
docker exec "$web" tar -xzf /tmp/recovery-data.tgz -C /var
docker cp "$root/scripts/upgrade-lab-inventory.php" "$web:/tmp/upgrade-lab-inventory.php"
test "$(docker exec -u www-data "$web" php /var/www/html/admin/cli/cfg.php --component=mod_lessonmark --name=version)" = 2026083001
docker exec -u www-data "$web" php /tmp/upgrade-lab-inventory.php > "$result/restored.json"
cmp "$backup/before.json" "$result/restored.json"
echo 'Full backup restoration passed: alpha2 and original LessonMark inventory match.'
echo 'Recovery containers have no published port, no cron and no external network.'
