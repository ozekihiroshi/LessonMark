# LessonMark 0.2.0 release evidence

Date: 2026-09-09. Status: preparation; NOT yet published.

## Candidate

- Release/build: 0.2.0 / 2026090901, stable metadata.
- Based on RC2 / 2026090802. No PHP/JavaScript/CSS runtime change from RC2.
- Final commit, hosted CI, ZIP digest, and publication are pending.

## Completed acceptance

- The 8095 user-authored course retained all 11 LessonMark records, source hashes,
  course/module IDs on alpha2-to-RC2 upgrade. See UPGRADE_LAB_RC2.md.
- Isolated full backup restoration reproduced the alpha2 inventory; a separate
  image fixture retained source, references and bytes through RC2 upgrade.
- The 8096 managed-code UI lab passed ZIP upgrade, Web/Cron recreation, and full
  DB/application/config/managed-code/image recovery in an isolated copy. Operational
  evidence belongs to the sibling moodle-rescue repository; it is not plugin code.
- AWS Moodle 5.2.2 / PHP 8.3.33 was upgraded by the owner through the UI from
  alpha2 to RC2. Code/DB versions and original inventory matched; Cron resumed.
- The owner restored the authored course from local Moodle to a new AWS course
  and confirmed Markdown display including diagrams. A user-inclusive backup also
  restored successfully. This is not a separate verification of every user role.
- Existing PHP 8.3/8.4, Chrome Behat, source-transfer API, backup/restore API,
  activity duplication, Japanese PDF regression and responsive UI evidence remains
  applicable to unchanged RC2 runtime code. Final stable CI must still pass.

## Open gates (do not record these as passed)

- Final exact-commit PHP 8.3/8.4, Chrome Behat, asset audit/rebuild and reproducible ZIP.
- Browser .md export/import round trip, browser print, whole-course copy UI:
  owner confirmation requested; older records cover APIs/activity duplication only.
- Cold-load rendering with external network blocked: previous browser tooling
  could not perform this runtime check. Local asset design alone is not a pass.
- Final stable ZIP installation/metadata upgrade smoke check.
- GitHub Release and Moodle Marketplace version submission.

No production data, credentials, SQL dumps, or site backup archives belong in
the public repository or release ZIP. Existing AWS/local sites are unchanged
by this release-preparation work.
