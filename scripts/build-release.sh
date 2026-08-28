#!/bin/sh
set -eu

repositoryroot="$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)"
cd "$repositoryroot"
output="${1:-build/mod_lessonmark.zip}"
case "$output" in
    /*) ;;
    *) output="$repositoryroot/$output" ;;
esac

for command in git tar zip unzip sha256sum awk; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "Required release command is unavailable: $command" >&2
        exit 1
    }
done
if [ -n "$(git status --porcelain --untracked-files=all)" ]; then
    echo "LessonMark repository must be clean before building a release ZIP." >&2
    exit 1
fi
git cat-file -e HEAD:plugin/lessonmark/version.php 2>/dev/null || {
    echo "Committed plugin source is unavailable." >&2
    exit 1
}

mkdir -p "$(dirname "$output")"
outputdirectory="$(cd "$(dirname "$output")" && pwd)"
output="$outputdirectory/$(basename "$output")"
buildroot="$(mktemp -d)"
temporaryzip="$buildroot/mod_lessonmark.zip"
trap 'rm -rf "$buildroot"' EXIT HUP INT TERM
mkdir -p "$buildroot/lessonmark"
git archive --format=tar HEAD:plugin/lessonmark | tar -xf - -C "$buildroot/lessonmark"
committimestamp="$(git show -s --format=%ct HEAD)"
find "$buildroot/lessonmark" -exec touch -d "@$committimestamp" {} +
(
    cd "$buildroot"
    find lessonmark -type f -print | LC_ALL=C sort | zip -X -q "$temporaryzip" -@
)
unzip -Z1 "$temporaryzip" | awk '
    $0 !~ /^lessonmark\// { invalid = 1 }
    $0 == "lessonmark/version.php" { version = 1 }
    $0 == "lessonmark/lib.php" { lib = 1 }
    $0 == "lessonmark/mod_form.php" { form = 1 }
    $0 == "lessonmark/view.php" { view = 1 }
    $0 == "lessonmark/db/install.xml" { installxml = 1 }
    $0 == "lessonmark/db/access.php" { access = 1 }
    $0 ~ /^lessonmark\/\.git/ { invalid = 1 }
    END { exit invalid || !version || !lib || !form || !view || !installxml || !access }
'
mv "$temporaryzip" "$output"
trap - EXIT HUP INT TERM
rm -rf "$buildroot"
echo "Plugin commit: $(git rev-parse HEAD)"
sha256sum "$output"

