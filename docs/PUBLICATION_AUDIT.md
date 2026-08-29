# Publication audit for LessonMark 0.1.0

This record maps the first stable release to the Moodle plugin publication
expectations and the repository's own release gates.

## Identity and packaging

- Component is `mod_lessonmark`; installation path is `mod/lessonmark`.
- The release ZIP has one root directory, `lessonmark/`.
- `version.php` declares Moodle 5.2 support and stable maturity.
- A complete GNU GPL v3 license is present at the repository root and Moodle
  file headers declare GPL v3 or later.
- The installable package includes English strings, README, change log,
  third-party declaration, and built AMD assets.

The GitHub repository is named `LessonMark`, rather than the older recommended
`moodle-mod_lessonmark` convention. This does not change the Moodle component,
package directory, upgrade identity, or ZIP validation and is therefore
recorded as a non-blocking naming difference.

## Third-party code

- PrismJS 1.29.0 is MIT licensed.
- `thirdpartylibs.xml` declares its version, license, and location.
- `vendor/prism/LICENSE` preserves the upstream license.
- `vendor/prism/readme_moodle.txt` records provenance and exact
  reconstruction steps.
- Generated AMD source carries provenance and the license notice.

## Security and privacy

- Preview and student display call the same renderer and sanitisation boundary.
- Raw HTML is not executable authoring syntax.
- Preview and export require a session key and the relevant capability.
- Student display and File API delivery enforce login, context, and capability.
- The plugin has no runtime external service, telemetry, credential, shell, or
  direct network dependency.
- The Privacy API null provider states that LessonMark stores course resource
  content but no personal data of its own.
- Vulnerabilities have a private GitHub reporting route; ordinary defects use
  the public issue tracker.

## Moodle lifecycle and quality

- Fresh install, RC-to-stable upgrade, course duplicate, backup, and restore are
  covered by CI and release smoke tests.
- Uninstall consequences are documented and checked on a disposable lifecycle
  environment.
- Moodle Plugin CI covers PHP lint, coding style, PHPDoc, validate, savepoints,
  PHPUnit, Chrome Behat, accessibility, JavaScript/CSS build consistency, and
  reproducible ZIP generation.
- The release tag is created only after the exact commit passes GitHub Actions.

## Public material

- Repository README, installation guide, authoring guide, contribution guide,
  security policy, issue tracker, release notes, listing copy, and screenshots
  are public.
- The final ZIP and SHA-256 checksum are attached to the GitHub Release.

Marketplace account submission is intentionally a separate manual publication
step and is not performed by this audit.
