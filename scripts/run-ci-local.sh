#!/bin/sh
set -eu

repositoryroot="$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)"
phpversion="${LESSONMARK_CI_PHP_VERSION:-8.3}"
moodlebranch="${LESSONMARK_CI_MOODLE_BRANCH:-MOODLE_502_STABLE}"
case "$phpversion" in
    8.3|8.4) ;;
    *)
        echo "LESSONMARK_CI_PHP_VERSION must be 8.3 or 8.4." >&2
        exit 1
        ;;
esac
case "$moodlebranch" in
    MOODLE_502_STABLE|main) ;;
    *)
        echo "LESSONMARK_CI_MOODLE_BRANCH must be MOODLE_502_STABLE or main." >&2
        exit 1
        ;;
esac

suffix="$$"
network="lessonmark-ci-net-$suffix"
database="lessonmark-ci-db-$suffix"
dbpassword="$(openssl rand -hex 18)"

cleanup() {
    case "$database" in
        lessonmark-ci-db-[0-9]*) docker rm -f "$database" >/dev/null 2>&1 || true ;;
    esac
    case "$network" in
        lessonmark-ci-net-[0-9]*) docker network rm "$network" >/dev/null 2>&1 || true ;;
    esac
}
trap cleanup EXIT HUP INT TERM

docker network create "$network" >/dev/null
docker run -d \
    --name "$database" \
    --network "$network" \
    -e MARIADB_ROOT_PASSWORD="$dbpassword" \
    mariadb:11.8 >/dev/null

attempt=0
until docker exec "$database" healthcheck.sh --connect --innodb_initialized >/dev/null 2>&1; do
    attempt=$((attempt + 1))
    if [ "$attempt" -ge 60 ]; then
        echo "Timed out waiting for the temporary MariaDB container." >&2
        exit 1
    fi
    sleep 1
done

docker run --rm \
    --network "$network" \
    -v "$repositoryroot:/workspace" \
    -e DB_HOST="$database" \
    -e DB_PASSWORD="$dbpassword" \
    -e MOODLE_BRANCH="$moodlebranch" \
    "moodlehq/moodle-php-apache:$phpversion" \
    sh /workspace/scripts/run-ci-in-container.sh
