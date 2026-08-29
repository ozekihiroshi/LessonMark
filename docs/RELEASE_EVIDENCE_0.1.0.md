# LessonMark 0.1.0 release evidence

Local acceptance was completed on 2026-08-29 in WSL with Moodle 5.2.2.

## Candidate

- Source commit used for the first reproducible package:
  `e26f5fa6d83588b2b14d63b00e21b433173b9c20`
- Release: `0.1.0`
- Plugin build: `2026082903`
- First package SHA-256:
  `a4a9157fc1d28b594c94df8dd794677595653c0628b4a9f83824ff152b11ef69`
- Two consecutive builds were byte-identical.
- The final tagged-commit checksum is published with the GitHub Release.

## Local quality gate

`scripts/run-ci-local.sh` completed successfully using PHP 8.3.33 and Moodle
5.2.2:

- PHP lint: 35 of 35 files;
- Moodle CodeSniffer, PHPDoc, plugin validation, and upgrade savepoint check;
- Grunt AMD, Gherkin, and CSS lint;
- PHPUnit: 24 tests and 87 assertions; and
- Composer advisory audit: no advisories.

The npm warning emitted while installing Moodle Plugin CI's own development
tooling is not a LessonMark runtime or release-ZIP dependency.

## Existing-site upgrade

The conventional UI-upload environment on port 8085 was upgraded from
`0.1.0-rc4` build `2026082902` to stable `0.1.0` build `2026082903`.
Moodle's non-interactive upgrade completed successfully, caches were purged,
and the site returned the expected HTTP login redirect.

M1, M3, M4, M5, and M6 smoke scripts all passed. The checks covered:

- preserved Markdown and non-executable raw HTML;
- table of contents, stable/duplicate heading IDs, callouts, code, and tables;
- permanent and draft Moodle File API images plus diagnostics;
- UTF-8 import normalisation, invalid/oversized rejection, safe export, and
  capability enforcement; and
- backup, restore, course duplicate, file preservation, and link remapping.

## Clean lifecycle

A separate disposable Compose project on port 8095 used dedicated containers,
networks, and volumes. It completed:

1. fresh Moodle install;
2. LessonMark 0.1.0 clean install;
3. M1 safety smoke;
4. Moodle CLI dry-run and uninstall of `mod_lessonmark`;
5. verification that LessonMark was absent from installed and missing lists;
6. reinstall from the same ZIP; and
7. a second successful M1 safety smoke.

The test also confirmed that Moodle maintenance CLI must run as the web-service
account. A deliberately detected root-owned cache condition reproduced the
known Moodle permission failure; correcting only those cache owners and using
`www-data` allowed the reinstall to complete. The disposable project and its
three volumes and two networks were then removed. The 8085 acceptance site and
its course data remained running and unchanged apart from the intended plugin
upgrade and generated smoke fixtures.
