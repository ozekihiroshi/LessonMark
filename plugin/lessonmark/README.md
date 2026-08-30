# LessonMark for Moodle

LessonMark is a Moodle activity module for creating and publishing teaching
material while Markdown remains the source of truth.

## Features

- create and edit Markdown inside Moodle without converting the saved source to HTML;
- responsive split Edit/Preview UI with keyboard-operable mobile tabs;
- one-time validated UTF-8 `.md` import and access-controlled source export;
- one safe rendering path for preview and student display;
- stable heading links, automatic contents, NOTE/TIP/WARNING callouts,
  highlighted fenced code, responsive tables, and teaching typography;
- ungraded RESPONSE and CHOICE working-answer blocks with browser-local draft
  retention, followed by native ANSWER disclosures on the same page;
- Moodle File API images with access control and author diagnostics; and
- backup, restore, course duplicate, and internal-link remapping.

Upload teaching images with the activity file manager and reference them as
`![Alternative text](@@PLUGINFILE@@/image.png)`. Subfolders are supported.
Import creates an unsaved editor change. Export downloads only the source
already saved in Moodle and does not include images.

Raw HTML is not supported authoring syntax. It is neutralised before Moodle's
HTML cleaning boundary rather than executed.

## Requirements

- Moodle 5.2
- PHP 8.3 or 8.4

## Installation

Install the release ZIP through **Site administration > Plugins > Install
plugins**, or extract this directory to `<moodle-root>/mod/lessonmark`. Complete
Moodle's normal database upgrade. The installed server does not need Composer
or Node.js.

Back up the database and `moodledata` before an upgrade. Uninstalling removes
LessonMark records and Moodle-managed activity files, so retain a course backup
or exported source first.

See the source repository for the authoring guide, installation details,
security policy, release checklist, and reproducible build scripts:
<https://github.com/ozekihiroshi/LessonMark>.

Use <https://github.com/ozekihiroshi/LessonMark/issues> for reproducible
non-security defects and feature discussions. Security reports must use the
private process in the repository's `SECURITY.md`.

## License

GNU GPL v3 or later. Bundled PrismJS assets are MIT licensed; see
`vendor/prism/LICENSE`, `vendor/prism/readme_moodle.txt`, and
`thirdpartylibs.xml`.
