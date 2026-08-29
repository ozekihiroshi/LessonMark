# Installation and upgrade

## Requirements

- Moodle 5.2
- PHP 8.3 or 8.4
- permission to install an activity module and complete the Moodle upgrade

LessonMark does not require Composer, Node.js, or a source checkout on the
Moodle server. Those tools are development dependencies only.

## Install from the release ZIP

1. Back up the database and `moodledata`.
2. Verify the published SHA-256 checksum for `mod_lessonmark.zip`.
3. In Moodle, open **Site administration > Plugins > Install plugins**.
4. Upload `mod_lessonmark.zip` and confirm the detected component is
   `mod_lessonmark` at `mod/lessonmark`.
5. Complete Moodle's database upgrade and review the plugin checks.
6. Create a LessonMark activity in a test course, preview it, save it, and view
   it with a student role before enabling broader use.

For a manual installation, extract the package so that `version.php` is at
`<moodle-root>/mod/lessonmark/version.php`, then run Moodle's normal upgrade.

Run Moodle maintenance CLI commands as the same operating-system account used
by the web process (commonly `www-data`). Running them as `root` can leave
root-owned files in `moodledata/cache` or `moodledata/localcache` and prevent
later web or CLI operations from creating cache directories.
Do not leave a nested `lessonmark/lessonmark` directory.

## Upgrade

Back up the site, replace only `<moodle-root>/mod/lessonmark` with the new
release contents, and complete the Moodle upgrade. Do not overwrite
`moodledata`; LessonMark images are Moodle-managed files. Purge caches if an
old editor script or stylesheet remains visible.

## Uninstall

Uninstalling removes LessonMark database records and Moodle-managed activity
files. Course backups or exported `.md` files must be created before uninstall
if the material is to be retained. Exported Markdown does not contain images;
use Moodle course backup when a restorable activity including images is needed.

## Support and security

Report reproducible non-security defects at
<https://github.com/ozekihiroshi/LessonMark/issues>. Security vulnerabilities
must be reported privately using the process in
<https://github.com/ozekihiroshi/LessonMark/security/policy>.
