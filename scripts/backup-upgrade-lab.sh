#!/bin/sh
# Back up the named 8095 lab only. Does not upgrade or uninstall anything.
set -eu
umask 077
root="$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)"
destination="$root/build/upgrade-lab/pre-rc2-$(date -u +%Y%m%dT%H%M%SZ)"
mkdir "$destination"
web=lessonmark-upgrade-lab
db=lessonmark-upgrade-lab-db
test "$(docker inspect -f '{{ index .Config.Labels "com.docker.compose.project" }}' "$web")" = lessonmark-upgrade-lab
test "$(docker inspect -f '{{ index .Config.Labels "com.docker.compose.project" }}' "$db")" = lessonmark-upgrade-lab
docker exec -u www-data "$web" php /var/www/html/admin/cli/maintenance.php --enable
trap 'docker start "$web" >/dev/null 2>&1 || true' EXIT
docker cp "$root/scripts/upgrade-lab-inventory.php" "$web:/tmp/upgrade-lab-inventory.php"
docker exec -u www-data "$web" php /tmp/upgrade-lab-inventory.php > "$destination/before.json"
docker exec "$web" tar -czf /tmp/lessonmark-pre-rc2-code.tgz -C /var/www html
docker cp "$web:/tmp/lessonmark-pre-rc2-code.tgz" "$destination/code-config.tgz"
docker stop "$web" >/dev/null
docker exec "$db" sh -c 'MYSQL_PWD="$MARIADB_ROOT_PASSWORD" mariadb-dump -uroot --single-transaction --routines --events --triggers --databases "$MARIADB_DATABASE"' > "$destination/database.sql"
image="$(docker inspect -f '{{.Image}}' "$web")"
docker run --rm --network none --entrypoint tar --volumes-from "$web:ro" \
    "$image" -czf - -C /var moodledata moodlebackups > "$destination/moodledata-backups.tgz"
# Configuration uses runtime environment variables; store a protected recovery copy.
cp "$root/build/upgrade-lab/runtime.env" "$destination/runtime.env"
docker inspect -f '{{.Image}}' "$web" > "$destination/image-id.txt"
test -s "$destination/database.sql"
tar -tzf "$destination/code-config.tgz" >/dev/null
tar -tzf "$destination/moodledata-backups.tgz" >/dev/null
(cd "$destination" && sha256sum before.json database.sql code-config.tgz moodledata-backups.tgz runtime.env image-id.txt > SHA256SUMS)
docker start "$web" >/dev/null
trap - EXIT
printf 'Backup verified (archive integrity, not yet restored): %s\n' "$destination"
printf '8095 remains in maintenance mode until upgrade checks complete.\n'
