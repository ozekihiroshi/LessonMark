# M1 implementation result

## Status

M1 is complete. `mod_lessonmark` can be installed from the release ZIP,
uninstalled with its database data and code directory removed, reinstalled,
and exercised as a Moodle course resource.

## Scope completed

- installable activity-module skeleton and XMLDB schema
- capability-aware CRUD and student-facing view
- Markdown source retained without HTML conversion
- one shared Moodle-core renderer adapter for display and future preview
- raw HTML neutralisation outside inline and fenced code
- privacy, completion, and viewed-event integration
- deterministic release ZIP builder and verifier
- PHPUnit, Moodle Code Checker, PHPDoc, validation, and savepoint gates
- GitHub Actions matrix for Moodle 5.2 on PHP 8.3 and PHP 8.4

## Administrator upload lifecycle

Environment: `moodle-rescue` UI test site on `http://localhost:8085`, Moodle
5.2.2, PHP 8.3, and MariaDB 11.8.

The generated `mod_lessonmark.zip` completed the conventional administrator
workflow through Moodle's own web forms and File API:

1. authenticated as the site administrator;
2. uploaded the ZIP to the install form draft area;
3. passed package validation as `mod_lessonmark`;
4. completed the Moodle database upgrade with `mod_lessonmark Success`;
5. uninstalled the plugin and deleted its database data;
6. confirmed and removed `/var/www/html/public/mod/lessonmark`;
7. verified that both the code directory and installed-plugin registration were absent;
8. uploaded and installed the same release ZIP again; and
9. reran the live M1 rendering and safety smoke test successfully.

The in-app browser could not start because of the host Windows ACL failure.
The lifecycle was therefore driven over the same authenticated administrator
HTTP forms, including the multipart File API upload, rather than by visual
click automation. It did not use Moodle's CLI installer.

## Automated verification

`scripts/run-ci-local.sh` creates an isolated Docker network and temporary
MariaDB, then runs Moodle Plugin CI 4.5.11 in Moodle's official PHP image. It
does not modify the running `moodle-rescue` sites. Node.js, NVM, and Composer
used by this runner are versioned and SHA-256 verified.

The Moodle 5.2 / PHP 8.3 run completed with:

- PHP lint: 18/18 files
- Moodle CodeSniffer: no errors or warnings
- Moodle PHPDoc Checker: passed
- plugin validation: passed
- upgrade savepoint check: passed; the initial empty upgrade function has no savepoint yet
- PHPUnit: 7 tests, 15 assertions

PHPUnit coverage metadata uses PHPUnit 11 attributes rather than deprecated
docblock annotations.

## CI and release artifact

`.github/workflows/moodle-plugin-ci.yml` runs the same gates on PHP 8.3 and
8.4 and separately builds the release ZIP twice, compares the bytes, verifies
the archive layout, and publishes it as a workflow artifact.

Run locally from WSL:

```sh
cd /mnt/d/workspace/LessonMark
scripts/run-ci-local.sh
scripts/build-release.sh
```

Set `LESSONMARK_CI_PHP_VERSION=8.4` to repeat the local gate on PHP 8.4.
The GitHub workflow will start after this repository is connected and pushed
to its GitHub remote.
