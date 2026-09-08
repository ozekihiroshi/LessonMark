#!/bin/sh
set -eu
umask 077
root="$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)"
backup="${1:?Provide the verified pre-upgrade backup directory}"
backup="$(cd "$backup" && pwd)"
case "$backup" in "$root"/build/upgrade-lab/pre-rc2-*) ;; *) exit 1 ;; esac
(cd "$backup" && sha256sum -c SHA256SUMS >/dev/null)
web=lessonmark-upgrade-lab
test "$(docker inspect -f '{{ index .Config.Labels "com.docker.compose.project" }}' "$web")" = lessonmark-upgrade-lab
test "$(docker exec -u www-data "$web" php /var/www/html/admin/cli/cfg.php --component=mod_lessonmark --name=version)" = 2026083001
zip="$root/build/mod_lessonmark-0.2.0-rc2.zip"
printf 'd9d09b9ae35c07fcdccbf35dc2169cbfdfbda3a67a34389b78fae49175294a5f  %s\n' "$zip" | sha256sum -c -
docker exec -u www-data "$web" php /tmp/upgrade-lab-inventory.php > "$backup/pre-apply.json"
cmp "$backup/before.json" "$backup/pre-apply.json"
docker cp "$zip" "$web:/tmp/lessonmark-rc2.zip"
docker exec "$web" sh -c 'set -eu; test ! -e /tmp/lessonmark-alpha2-before-rc2; stage=$(mktemp -d /tmp/lessonmark-rc2.XXXXXX); unzip -q /tmp/lessonmark-rc2.zip -d "$stage"; test -f "$stage/lessonmark/version.php"; mv /var/www/html/public/mod/lessonmark /tmp/lessonmark-alpha2-before-rc2; mv "$stage/lessonmark" /var/www/html/public/mod/lessonmark; chown -R www-data:www-data /var/www/html/public/mod/lessonmark'
docker exec -u www-data "$web" php /var/www/html/admin/cli/upgrade.php --non-interactive
test "$(docker exec -u www-data "$web" php /var/www/html/admin/cli/cfg.php --component=mod_lessonmark --name=version)" = 2026090802
docker exec -u www-data "$web" php /tmp/upgrade-lab-inventory.php > "$backup/after.json"
cmp "$backup/before.json" "$backup/after.json"
docker cp "$root/scripts/upgrade-lab-render-check.php" "$web:/tmp/upgrade-lab-render-check.php"
docker exec -u www-data "$web" php /tmp/upgrade-lab-render-check.php
docker exec -u www-data "$web" php /var/www/html/admin/cli/purge_caches.php
docker exec -u www-data "$web" php /var/www/html/admin/cli/maintenance.php --disable
printf 'RC2 upgrade and inventory comparison succeeded. No uninstall was performed.\n'
