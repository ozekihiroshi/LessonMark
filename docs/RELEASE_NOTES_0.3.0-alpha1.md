# LessonMark 0.3.0-alpha1

An evaluation release for presenting a sequence of Markdown lessons and for
producing more predictable printed teaching material.

## New since 0.2.0

- Course presentation follows accessible, visible LessonMark activities in
  Moodle course order without creating a second copy of the lesson content.
- Lesson selection, course/slide position, keyboard navigation, and a persistent
  fullscreen shell support classroom delivery across lesson boundaries.
- Explicit `<!-- slide -->` boundaries now start new pages in browser printing
  and **Download saved PDF**, while normal study view remains continuous.
- Browser printing expands saved ANSWER disclosures, groups image headings with
  their images where practical, and restores the learner's disclosure state
  after printing.
- Screen typography, tables, code, and focus indicators are refined without
  changing the saved Markdown source.

## Requirements and limits

- Moodle 5.2, PHP 8.3 or 8.4.
- This is an alpha prerelease for evaluation, not the stable 0.3 release.
- Course presentation includes only visible, course-listed LessonMark activities
  the current user can access. It does not bypass Moodle availability rules.
- Course-wide PDF export is not included. Print or download each LessonMark
  activity separately.
- A slide longer than one printed page continues naturally onto additional
  pages. The next explicit slide still begins on a new page.
- Browser-local working answers and unsaved editor changes are not included in
  saved-content PDF export.

## Upgrade safely

Back up the database, moodledata, installed plugin code, and configuration.
Install the verified ZIP through Moodle's plugin installer and complete the
normal upgrade. Do not uninstall the existing plugin first, because uninstalling
removes LessonMark activities and their managed files.
