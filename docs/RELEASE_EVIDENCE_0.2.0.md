# LessonMark 0.2.0 release evidence

Date: 2026-09-09. Status: preparation; NOT yet published.

## Previous candidate (superseded; do not publish)

- Release/build: 0.2.0 / 2026090901, stable metadata.
- Based on RC2 / 2026090802. No PHP/JavaScript/CSS runtime change from RC2.
- Candidate commit: `00333eea19c1776a2d1acf572e4bc694f73fbbfb`.
- Hosted CI: https://github.com/ozekihiroshi/LessonMark/actions/runs/34314877607
- Reproducible ZIP and asset audit/rebuild passed; npm reported zero vulnerabilities.
- Candidate ZIP SHA-256:
  `377071a5f16cfeecd50e44858688cb81c01aa09d8ad8d2acd84ffd91594368e5`.
- Downloaded CI artifact was checked against its outer archive digest and the
  inner plugin ZIP digest. Local file: `build/mod_lessonmark-0.2.0.zip` (ignored).
- PHP 8.3 and PHP 8.4 passed all applicable gates. Chrome Behat and automated
  accessibility passed on PHP 8.3 (the workflow does not run Behat on PHP 8.4).
- These identifiers apply to the candidate commit above, not a later evidence-only
  commit. Publication is pending.

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

- Current candidate: 0.2.0 / 2026090903. The owner found Moodle's floating `?`
  footer button overlapping a Mermaid node in browser print and Save as PDF.
  Screen rendering was unaffected. A print-only rule now hides `#page-footer`
  under `#page-mod-lessonmark-view`; visual retest and new exact-commit CI required.
  The build 2026090901 ZIP and its successful CI above do not cover this change.
- Build 2026090902 CI failed only CSS lint (`declaration-no-important`). PHP 8.3/8.4
  PHPUnit and Chrome Behat passed. Build 2026090903 removes the unnecessary
  `!important`; the page/footer ID selector scopes print hiding to LessonMark.
- The owner later reported no question-mark overlay in print, including on AWS;
  the deployed build was not established, so this is not attributed to the fix.
- The owner's response identified browser print and whole-course copy as untested;
  .md export/import was understood as confirmed. Older records cover source APIs
  and activity duplication separately.
- Browser print and whole-course copy UI remain untested.
- Cold-load rendering with external network blocked: previous browser tooling
  could not perform this runtime check. Local asset design alone is not a pass.
- Final stable ZIP installation/metadata upgrade smoke check.
- GitHub Release and Moodle Marketplace version submission.

No production data, credentials, SQL dumps, or site backup archives belong in
the public repository or release ZIP. Existing AWS/local sites are unchanged
by this release-preparation work.
