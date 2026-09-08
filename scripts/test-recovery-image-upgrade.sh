#!/bin/sh
# Upgrade only the internal recovery copy after its synthetic images exist.
set -eu
umask 077
root="$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)"
out="$root/build/upgrade-lab/recovery-check"
web=lessonmark-recovery-check
test "$(docker exec -u www-data "$web" php /var/www/html/admin/cli/cfg.php --name=dbhost)" = lessonmark-recovery-check-db
test "$(docker exec -u www-data "$web" php /var/www/html/admin/cli/cfg.php --component=mod_lessonmark --name=version)" = 2026083001
test ! -f "$out/images-before.json"
docker exec -u www-data "$web" php /tmp/recovery-image-fixture.php check
docker exec -u www-data "$web" php /tmp/upgrade-lab-inventory.php > "$out/images-before.json"
zip="$root/build/mod_lessonmark-0.2.0-rc2.zip"
printf 'd9d09b9ae35c07fcdccbf35dc2169cbfdfbda3a67a34389b78fae49175294a5f  %s\n' "$zip" | sha256sum -c -
docker cp "$zip" "$web:/tmp/lessonmark-rc2.zip"
docker exec "$web" sh -c 'set -eu; test ! -e /tmp/lessonmark-alpha2-image-test; stage=$(mktemp -d /tmp/lessonmark-image-upgrade.XXXXXX); unzip -q /tmp/lessonmark-rc2.zip -d "$stage"; test -f "$stage/lessonmark/version.php"; mv /var/www/html/public/mod/lessonmark /tmp/lessonmark-alpha2-image-test; mv "$stage/lessonmark" /var/www/html/public/mod/lessonmark; chown -R www-data:www-data /var/www/html/public/mod/lessonmark'
docker exec -u www-data "$web" php /var/www/html/admin/cli/upgrade.php --non-interactive
test "$(docker exec -u www-data "$web" php /var/www/html/admin/cli/cfg.php --component=mod_lessonmark --name=version)" = 2026090802
docker exec -u www-data "$web" php /tmp/upgrade-lab-inventory.php > "$out/images-after.json"
cmp "$out/images-before.json" "$out/images-after.json"
docker exec -u www-data "$web" php /tmp/recovery-image-fixture.php check
printf 'PASS: alpha2 to RC2 preserved every lesson record, source, ID, image path and image content hash.\n'
