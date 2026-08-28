# LessonMark

LessonMark is a Moodle course resource for authoring, previewing, and
publishing teaching material whose source of truth remains Markdown.

The plugin component is `mod_lessonmark`. The initial target is Moodle 5.2 on
PHP 8.3 and 8.4.

## Repository layout

```text
LessonMark/
├── .github/workflows/     GitHub Actions quality and release gates
├── docs/                  Product, technical, and implementation records
├── plugin/lessonmark/     Installable mod_lessonmark source
├── scripts/               Release, smoke, and local CI runners
└── LessonMark.code-workspace
```

The reusable Moodle Docker environments remain in the separate
`D:\workspace\moodle-rescue` repository. The UI upload environment is exposed
on port 8085 and contains no LessonMark source mount.

## WSL workflow

```sh
cd /mnt/d/workspace/LessonMark
scripts/run-ci-local.sh
scripts/build-release.sh
```

The local CI runner creates and removes only its own temporary Docker network,
database container, and PHP test container. It runs the Moodle PHP gates,
Grunt JavaScript/CSS checks, AMD generation consistency, and PHPUnit. Use PHP
8.4 explicitly with:

```sh
LESSONMARK_CI_PHP_VERSION=8.4 scripts/run-ci-local.sh
```

The release artifact is written to `build/mod_lessonmark.zip`. The builder
requires a clean Git worktree, archives only committed plugin files, validates
the ZIP layout, and produces byte-identical output for the same commit.

## Current status

M1 through M4 are complete. LessonMark can be installed through Moodle's plugin
upload UI, stores Markdown without HTML conversion, and provides a dedicated
two-pane editor with save-free shared preview. Teaching documents include
stable heading links, an automatic table of contents, NOTE/TIP/WARNING
callouts, syntax-highlighted code, responsive tables, and teaching typography.
Images now follow Moodle's draft and permanent File API lifecycle, use canonical
`@@PLUGINFILE@@` references, and are checked for relative paths and missing
alternative text. Development proceeds to M5: `.md` import and export.

## Documents

- [Product requirements](docs/PRODUCT_REQUIREMENTS.md)
- [Technical decisions and milestones](docs/TECHNICAL_DECISIONS.md)
- [Authoring guide](docs/AUTHORING_GUIDE.md)
- [M1 implementation result](docs/M1_IMPLEMENTATION.md)
- [M2 implementation result](docs/M2_IMPLEMENTATION.md)
- [M3 implementation result](docs/M3_IMPLEMENTATION.md)
- [M4 implementation result](docs/M4_IMPLEMENTATION.md)

## GitHub

The repository is published at <https://github.com/ozekihiroshi/LessonMark>.
`.github/workflows/moodle-plugin-ci.yml` runs the Moodle quality and release
gates for pushes and pull requests.
