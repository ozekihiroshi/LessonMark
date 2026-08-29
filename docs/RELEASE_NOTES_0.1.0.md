# LessonMark 0.1.0

LessonMark 0.1.0 is the first stable release of the Markdown-first Moodle
teaching resource.

## Highlights

- Create, import, edit, preview, and publish Markdown without converting the
  stored source to generated HTML.
- Use a responsive split authoring surface, or keyboard-operable Edit/Preview
  tabs on narrow screens.
- Publish automatic contents, stable heading links, code highlighting, tables,
  NOTE/TIP/WARNING callouts, teaching typography, and Moodle-managed images.
- Export saved Markdown and preserve complete activities through Moodle
  backup, restore, and course duplicate.
- Use the same safe rendering boundary for unsaved Preview and student display.

## Compatibility

- Moodle 5.2
- PHP 8.3 or 8.4

No Composer, Node.js, external service, or subscription is required on the
Moodle server.

## Upgrade

Back up the database and `moodledata`, install the ZIP through Moodle's normal
plugin installer or replace `mod/lessonmark`, then complete the database
upgrade. See `docs/INSTALLATION.md` for lifecycle details.

The release ZIP and checksum are generated reproducibly from the tagged commit.
