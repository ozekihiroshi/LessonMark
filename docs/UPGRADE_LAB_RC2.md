# RC2 isolated upgrade laboratory

## Scope

AWS source reported by the owner: LessonMark 0.2.0-alpha2 (2026083001),
Moodle 5.2.2, PHP 8.3.33. AWS and the existing 8085 site must retain content.
Do not uninstall LessonMark on either site.

Use `docker-compose.ui-test.yml` from the sibling moodle-rescue repository,
with project/container prefix `lessonmark-upgrade-lab` and port 8095.
Dedicated volumes: `lessonmark-upgrade-lab_ui_test_db`,
`lessonmark-upgrade-lab_ui_test_moodle_data`,
`lessonmark-upgrade-lab_ui_test_backups`. No source mount or shared data volume.
Run `sh scripts/prepare-upgrade-lab.sh` in WSL. Generated test credentials are
in ignored `build/upgrade-lab/runtime.env`; never publish that file.

## Candidate identity

- RC2 package commit: `685c126d591e027c6b2a538ccec4e227f9885784`
- Release/build: `0.2.0-rc2` / `2026090802`
- File: `build/mod_lessonmark-0.2.0-rc2.zip`
- SHA-256: `d9d09b9ae35c07fcdccbf35dc2169cbfdfbda3a67a34389b78fae49175294a5f`
- Two builds matched; release metadata/package/third-party hash validation passed.
- No GitHub Release or AWS deployment performed.

Alpha2 test ZIP is reconstructed from Git commit `2537798`, whose metadata is
0.2.0-alpha2 / 2026083001. SHA-256:
`1811fecd80eaf1eee3008009460ab6ef32a6675e45cc542e6afc27688eca6f41`.
This does not establish that AWS has
no local modifications. Compare deployed source if modifications are possible.

## Gates, in order

1. Complete isolated Moodle initialization and confirm Moodle/PHP versions.
2. Install alpha2 and create representative Markdown, image and settings fixtures.
3. Capture source/file hashes, course/module IDs and a complete pre-upgrade
   backup of DB, moodledata and matching code/configuration.
4. Upgrade in place to the exact RC2 ZIP. Never uninstall as an upgrade step.
5. Compare preserved fixtures; exercise normal view, editing, PDF and slides.
6. Restore the matched pre-upgrade snapshot and verify alpha2 fixtures.
7. Only in this disposable lab, test uninstall/reinstall and fresh RC2 install.
8. Then update 8085 with backup and manual acceptance. AWS is a separate,
   subsequently authorized operation after the local gates pass.

Current status: dedicated Moodle initialization and alpha2 CLI installation
completed with exit 0. Installed plugin version is `2026083001`; PHP is 8.3.33
and Moodle upgrade output identifies 5.2.2 (20260810 / 2026042002).
Alpha2 was installed from the staged ZIP, not through the browser upload UI.
Both ZIPs are staged under `/tmp` in the dedicated web container.
Fixtures, pre-upgrade backup, RC2 upgrade, recovery and uninstall tests remain.

## User-authored course upgrade result

The owner created 11 LessonMark activities and downloaded a course backup.
Do not treat these as disposable: the owner intends to reuse this material.
Future destructive lifecycle checks must use a separate copy or get fresh
approval for the exact targets, not remove this material from 8095.

Full backup: `build/upgrade-lab/pre-rc2-20260908T075649Z` (ignored, sensitive).
Contains DB dump, all Moodle code/config, moodledata and backup volume archives,
runtime environment, image identity, inventory and SHA256SUMS. Taken with
maintenance enabled and the web container stopped during DB/data capture.
Archive integrity and hashes passed; restoration has NOT yet been tested.

The exact RC2 ZIP above was installed without uninstalling alpha2. Moodle CLI
upgrade succeeded and installed version was verified as 2026090802.
All 11 full LessonMark records, source hashes and course/module IDs matched
before/after. Inventory SHA256 on both sides:
`8a7c67943b5e06895b695eea995ea47cb9e04b94b8b7a983b44b92b2e65cd8f1`.
There were no managed images in these activities; image preservation remains
a separate test, not a passed check here.

The first CLI rendering check failed because the test harness omitted Moodle
filelib.php. Adding that dependency to the harness resolved the error without
changing the plugin. Server-side HTML, per-slide rendering and PDF generation
then passed for all 11 activities. This is not browser visual validation.
Caches were purged and maintenance disabled successfully. AWS, 8085, 8083 and
8084 were not changed. Restore rehearsal and remaining manual checks are open.

### Recovery rehearsal preparation (blocked before execution)

Prepared `scripts/restore-lab-copy.sh` to restore the matched snapshot into
new internal-only `lessonmark-recovery-check` containers and dedicated volumes.
It refuses existing target containers/volumes, publishes no port, runs no cron,
and compares restored alpha2 inventory with the original. It has NOT run yet.
All six backup manifest entries were independently rehashed on Windows and
matched. WSL Docker status and a subsequent `/bin/true` both failed with
`Wsl/Service/0x8007274c`, preventing execution. No WSL restart, recovery write,
image fixture insertion or AWS change was performed. Both remaining tests
are still open; file integrity alone is not proof of successful restoration.

### Recovery and image upgrade results

The owner ran the restore script from the existing WSL terminal and reported
success. The generated `recovery-check/restored.json` was independently hashed
and matched `before.json` (8a7c67943b5e06895b695eea995ea47cb9e04b94b8b7a983b44b92b2e65cd8f1).
This verifies restored alpha2 and all 11 original LessonMark records/identifiers.

WSL tool access recovered. A dedicated synthetic course was added ONLY to
`lessonmark-recovery-check`, guarded by the exact recovery DB hostname. It
contains two 80x48 red/blue PNGs, `images/test.png` and `images/確認.png`.
Both alpha2 and RC2 passed content decoding, managed HTML reference rewriting
and PDF image embedding checks. The same pinned RC2 ZIP was installed in place.
The image-upgrade runner exited 0; before/after inventories match byte for byte
for all 12 activities, including source, complete LessonMark record, course/module
IDs, image paths, Moodle content hashes and SHA256 of actual image bytes.

Evidence: ignored `build/upgrade-lab/recovery-check/images-before.json` and
`images-after.json`. The internal-only recovery copy now runs RC2. No port was
published, no cron was started, and 8095, 8085 and AWS were not modified.
These checks verify storage and server-side rendering/PDF embedding, not browser
visual appearance or authenticated pluginfile delivery. No AWS deployment or
new public release was performed. The separately retained pre-RC2 full backup
remains unchanged and available for recovery.
