# M1 implementation result

## Scope completed

- `mod_lessonmark` activity-module skeleton
- installable XMLDB schema and CRUD callbacks
- Markdown source stored without HTML conversion
- shared Moodle-core Markdown renderer adapter
- raw HTML neutralisation outside inline and fenced code
- student-facing `view.php`
- capability, privacy, completion, and viewed-event integration
- PHPUnit test sources
- deterministic release ZIP builder
- Moodle 5.2.2 integration smoke script

## Local verification

Environment: `moodle-rescue` UI test Compose project, Moodle 5.2.2, PHP 8.3, MariaDB 11.8.

The release ZIP installed as `mod_lessonmark`. A fixture course and activity were created through Moodle APIs with the following successful checks:

- Markdown source preserved
- heading rendered
- strong emphasis rendered
- raw script element not executable
- raw HTML visible as text
- inline HTML example rendered as code
- authenticated student-facing HTTP response returned 200

## Release artifact

Run from WSL:

```sh
cd /mnt/d/workspace/LessonMark
scripts/build-release.sh
```

The builder requires a clean Git repository, archives only committed `plugin/lessonmark` source, validates the ZIP layout, and prints the artifact SHA-256.

Two consecutive builds must be byte-identical before release.

## UI environment adjustment

The `moodle-rescue` UI test image originally allowed web installation only below `public/admin/tool`. Its UI-only Docker target now also grants the web installer ownership of `public/mod`. Local source-mounted and production-shaped images are unchanged.

## Remaining verification

The in-app Browser could not start because of the host ACL failure affecting browser automation. The ZIP was therefore placed in the same UI test container and installed with Moodle's official CLI upgrade path. The conventional administrator multipart-upload screen remains a manual lifecycle check.

The PHPUnit test files are present but were not executed because this disposable UI image is not configured as a PHPUnit development installation. Their behavior was covered by the live Moodle smoke test; formal PHPUnit execution remains part of CI setup.

