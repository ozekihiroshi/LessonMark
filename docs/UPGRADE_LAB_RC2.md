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
