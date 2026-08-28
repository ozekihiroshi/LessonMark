#!/bin/sh
set -eu

: "${DB_HOST:?DB_HOST is required}"
: "${DB_PASSWORD:?DB_PASSWORD is required}"
: "${MOODLE_BRANCH:=MOODLE_502_STABLE}"

cibase="$(mktemp -d /tmp/lessonmark-plugin-ci.XXXXXX)"
trap 'rm -rf "$cibase"' EXIT HUP INT TERM
cd "$cibase"

nodearchive="node-v22.23.2-linux-x64.tar.gz"
curl -fsSLo "$nodearchive" "https://nodejs.org/dist/v22.23.2/$nodearchive"
echo "b294a556e639d64338823920e5866c21c02741742d2e1529ee1a225c1ec9252a  $nodearchive" | sha256sum -c -
tar -xzf "$nodearchive"

NVM_DIR="$cibase/nvm"
export NVM_DIR
mkdir -p "$NVM_DIR/versions/node"
mv "$cibase/node-v22.23.2-linux-x64" "$NVM_DIR/versions/node/v22.23.2"
curl -fsSLo "$NVM_DIR/nvm.sh" https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.3/nvm.sh
echo "390260ab9eb1da20e8bc0ebea2ee90f528d53e5e9f6e13b16717db4af454df9d  $NVM_DIR/nvm.sh" | sha256sum -c -
set +u
. "$NVM_DIR/nvm.sh"
set -u
nvm use 22.23.2 >/dev/null

composerphar="$cibase/composer.phar"
curl -fsSLo "$composerphar" https://getcomposer.org/download/2.10.3/composer.phar
echo "7a2d379d5b8ffdaa028580ef26494c36d2feef4b178d3dd1473a4dbc5e17c8d6  $composerphar" | sha256sum -c -
chmod 0755 "$composerphar"
ln -s "$composerphar" "$NVM_BIN/composer"

php "$composerphar" create-project \
    --no-interaction \
    --no-dev \
    --prefer-dist \
    moodlehq/moodle-plugin-ci:4.5.11 ci

PATH="$cibase/ci/bin:$cibase/ci/vendor/bin:$PATH"
export PATH
export DB=mariadb
export MOODLE_BRANCH
export LANG=C.UTF-8

moodle-plugin-ci install \
    --plugin /workspace/plugin/lessonmark \
    --db-host="$DB_HOST" \
    --db-user=root \
    --db-pass="$DB_PASSWORD" \
    --db-name=moodle \
    --no-plugin-node
moodle-plugin-ci phplint
moodle-plugin-ci phpcs --max-warnings 0
moodle-plugin-ci phpdoc --max-warnings 0
moodle-plugin-ci phpcpd
moodle-plugin-ci validate
moodle-plugin-ci savepoints
if ! moodle-plugin-ci grunt --max-lint-warnings 0; then
    (cd "$cibase/moodle" && npx grunt amd --root=public/mod/lessonmark)
    builddir="$cibase/moodle/public/mod/lessonmark/amd/build"
    for module in editor prism-languages syntax-highlighter; do
        if [ ! -f "$builddir/$module.min.js" ] || [ ! -f "$builddir/$module.min.js.map" ]; then
            echo "Generated AMD artifacts for $module were not found." >&2
            exit 1
        fi
        cp "$builddir/$module.min.js" "$builddir/$module.min.js.map" \
            /workspace/plugin/lessonmark/amd/build/
    done
    moodle-plugin-ci grunt --max-lint-warnings 0
fi
moodle-plugin-ci phpunit --fail-on-warning
