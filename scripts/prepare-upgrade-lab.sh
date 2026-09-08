#!/bin/sh
# Create only the isolated LessonMark upgrade lab; never operate the 8085 project.
set -eu
umask 077
repositoryroot="$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)"
rescueroot="$(dirname "$repositoryroot")/moodle-rescue"
runtime="$repositoryroot/build/upgrade-lab"
mkdir -p "$runtime"
envfile="$runtime/runtime.env"
if [ ! -f "$envfile" ]; then
    {
        printf 'UI_TEST_COMPOSE_PROJECT_NAME=lessonmark-upgrade-lab\n'
        printf 'UI_TEST_CONTAINER_PREFIX=lessonmark-upgrade-lab\n'
        printf 'UI_TEST_MOODLE_PORT=8095\nUI_TEST_WWWROOT=http://localhost:8095\n'
        printf 'UI_TEST_MOODLE_IMAGE=moodle-rescue-ui-test-moodle\n'
        printf 'UI_TEST_DB_NAME=lessonmark_upgrade_lab\nUI_TEST_DB_USER=lessonmark_upgrade_lab\n'
        printf 'UI_TEST_DB_PASSWORD=%s\n' "$(openssl rand -hex 24)"
        printf 'UI_TEST_DB_ROOT_PASSWORD=%s\n' "$(openssl rand -hex 24)"
        printf 'UI_TEST_ADMIN_PASSWORD=Lab-%s!\n' "$(openssl rand -hex 24)"
    } > "$envfile"
fi
# CLI overrides prevent ambient shell values from selecting the shared project.
export UI_TEST_COMPOSE_PROJECT_NAME=lessonmark-upgrade-lab
export UI_TEST_CONTAINER_PREFIX=lessonmark-upgrade-lab
export UI_TEST_MOODLE_PORT=8095
export UI_TEST_WWWROOT=http://localhost:8095
export UI_TEST_MOODLE_IMAGE=moodle-rescue-ui-test-moodle
docker compose --env-file "$envfile" -f "$rescueroot/docker-compose.ui-test.yml" \
    -p lessonmark-upgrade-lab config --quiet
docker compose --env-file "$envfile" -f "$rescueroot/docker-compose.ui-test.yml" \
    -p lessonmark-upgrade-lab up -d --no-build --pull never
