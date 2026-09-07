# LessonMark 0.2.0-rc1 release evidence

Date: 2026-09-07 (Asia/Tokyo)

## Candidate source and artifact

- Plugin source commit: `a7167c7a83af1bd1e93e453ad148eeef3faa6495`
- Release: `0.2.0-rc1`
- Moodle build number: `2026090702`
- Artifact: `build/mod_lessonmark-0.2.0-rc1.zip`
- SHA-256: `54d316e7c5a86057f3a902973b2e0de7e3c151588d4addbe9f4349d799f48af5`

The artifact was built twice from the clean source commit and compared with
`cmp`. Both builds produced the digest above. `scripts/verify-release.php`
accepted the component, release/maturity metadata, ZIP layout, required third-
party files and licenses, and exact KaTeX, AsciiMath, and Mermaid asset hashes.

## Browser-asset supply chain

- Clean `npm ci` completed with 118 packages.
- `npm audit --audit-level=high` reported 0 vulnerabilities for LessonMark.
- `npm run build:assets` reproduced KaTeX 0.18.5, asciimath-parser 0.6.11,
  and Mermaid 11.17.2 committed assets without a diff.
- The build used Node 22.23.2 inside Linux; the downloaded Node archive and nvm
  bootstrap were SHA-256 verified by the local CI runner.

## Moodle 5.2 / PHP 8.3

The complete local container gate passed against Moodle 5.2.2+ build 20260903
and PHP 8.3.33:

- PHP lint: 40/40 files
- Moodle CodeSniffer: 0 errors and 0 warnings
- PHPDoc checker: passed
- plugin validation: passed
- upgrade savepoint check: passed with the existing informational empty-
  upgrade-function note
- Grunt JavaScript/Gherkin/CSS checks: passed
- PHPUnit: 32 tests, 127 assertions

## Moodle 5.2 / PHP 8.4

On PHP 8.4.24, browser-asset reproduction, PHP lint (40/40), Moodle
CodeSniffer, PHPDoc, plugin validation, and savepoint checks passed. The local
WSL Docker backend restarted during the Grunt ESLint stage; the temporary
container exited with status 255 and produced no lint diagnostic. This is not
recorded as a passing complete PHP 8.4 run. The clean hosted GitHub Actions
matrix remains required for PHP 8.4 Grunt and PHPUnit evidence.

## Browser and upload gates still required

- Push the candidate branch and require the GitHub Actions PHP 8.3/8.4 matrix,
  Chrome Behat (including malformed formula/diagram fallback), and release ZIP
  job to pass.
- Upload this exact ZIP in the non-source-mounted Moodle UI environment and
  complete the install/upgrade, Offline Preview/student view, import/export,
  image, backup/restore, duplicate, PDF, and lifecycle checks.

Windows computer-use automation was unavailable during this run because its
kernel assets could not be written (`path not found`). No UI upload action was
performed.
