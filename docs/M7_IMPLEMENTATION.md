# M7 release-quality implementation

M7 closes the v0.1 release-candidate boundary. The supported production target
is Moodle 5.2 on PHP 8.3 or 8.4. Moodle 5.3 is not yet released at the time of
this decision, so its development branch is an informational compatibility
probe rather than a support claim.

## Delivered controls

- Moodle Plugin CI matrix for supported PHP versions, with lint, coding style,
  PHPDoc, validation, savepoints, Grunt, PHPUnit, and Chrome Behat
  acceptance/accessibility gates.
- Keyboard-operable Edit/Preview tabs on narrow screens, roving tab focus,
  labelled panels, preview busy state, and responsive two-pane semantics.
- Regression tests for raw HTML neutralisation, unsafe link schemes, invalid
  UTF-8, and the audited Privacy API null-provider declaration.
- Reproducible release ZIP validation, required release-file checks, unsafe
  path checks, release metadata checks, and SHA-256 output.
- Installation, upgrade, uninstall, security-reporting, and release-checklist
  documentation.

## Local compatibility evidence

- Moodle 5.2.2+ (Build 20260818), PHP 8.3.33: all quality gates passed;
  PHPUnit passed 24 tests and 87 assertions.
- Moodle 5.2.2+ (Build 20260818), PHP 8.4.24: all quality gates passed;
  PHPUnit passed 24 tests and 87 assertions.
- Moodle 5.3dev (Build 20260818), PHP 8.4.24: the informational probe passed
  the same gates and PHPUnit count. This does not expand the supported range.

The exact release commit, ZIP SHA-256, UI upload lifecycle result, and GitHub
Actions result are reported with the release candidate. A failed or unavailable
gate remains explicit; it is not converted into a support claim.
