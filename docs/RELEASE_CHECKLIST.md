# Release checklist

## Scope and metadata

- Confirm the product requirements and supported authoring syntax are unchanged.
- Set a unique ten-digit plugin version, semantic release, maturity, Moodle
  requirement, and supported range in `version.php`.
- Update `CHANGELOG.md`, user documentation, and third-party attribution.

## Automated gates

- Run Moodle Plugin CI on PHP 8.3 and PHP 8.4 against Moodle 5.2.
- Pass PHP lint, Moodle Code Checker, PHPDoc, copy/paste detection, plugin
  validation, savepoints, Grunt, and PHPUnit.
- Pass the Chrome Behat flow for authoring, preview, publishing, and automated
  accessibility checks.
- Treat Moodle `main` results as informational until the corresponding stable
  release becomes a supported target.

## Security, privacy, and accessibility

- Verify raw active HTML and dangerous URL schemes remain inert.
- Verify preview, export, File API delivery, and editing enforce sesskey and
  capabilities as applicable.
- Re-audit the Privacy API declaration whenever user-linked storage,
  preferences, logging, or external data transfer is added.
- Test keyboard-only editor tab operation, focus visibility, labels, semantic
  headings, callouts, code, tables, responsive layout, and screen-reader names.

## Package and lifecycle

- Build twice from the same clean commit and compare the ZIP bytes.
- Run `php scripts/verify-release.php <version> build/mod_lessonmark.zip`.
- Record the commit and SHA-256 digest.
- Install the ZIP through Moodle's plugin upload UI in a non-source-mounted
  environment with developer debugging enabled.
- Test a clean install, upgrade from the preceding version, activity creation,
  preview, student display, import/export, image access, backup/restore, course
  duplicate, and uninstall/reinstall when the release changes storage.
- Confirm no source repository, Composer install, or Node.js build is needed on
  the Moodle server.

## Publication

- Review the Git diff and GitHub Actions result for the exact commit.
- Attach only the verified ZIP and publish its SHA-256 digest.
- Retain test evidence and document any deferred compatibility work.
