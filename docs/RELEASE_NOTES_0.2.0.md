# LessonMark 0.2.0

Markdown teaching documents for Moodle, now with mathematics, diagrams,
same-page practice, PDF download, and classroom presentation.

## New since 0.1.0

- Locally bundled LaTeX and AsciiMath formula rendering and Mermaid diagrams.
- Responsive Markdown editor/Preview and saved student rendering share the
  same safe source-processing path. Invalid browser syntax retains readable source.
- Ungraded RESPONSE/CHOICE working answers and ANSWER disclosures. Working
  answers stay in the browser; they are not Moodle submissions or grades.
- Saved-content PDF export with managed images and Japanese code-block text.
- Classroom slides from saved Markdown, keyboard navigation, fullscreen request,
  slide count, and return to the lesson. Course-wide slide navigation is not included.

## Requirements and limits

- Moodle 5.2, PHP 8.3 or 8.4.
- No CDN, external rendering service, subscription, Composer, or Node.js is
  required on the Moodle server.
- PDF retains formulas and Mermaid diagrams as readable source; browser-rendered
  graphics are not embedded. Browser-local working answers are not exported.
- Markdown is still the stored source, including in import/export and backups.

## Upgrade safely

Back up the database, moodledata, installed plugin code, and configuration.
Upload `mod_lessonmark.zip` through Site administration > Plugins > Install plugins
and complete Moodle's normal upgrade. Do not uninstall the existing plugin first:
uninstall removes its activities and managed files.

The stable build is `2026090901`, higher than RC2 (`2026090802`). It changes release
metadata/documentation, not RC2 runtime behavior. Docker deployments must retain
updated plugin code across Web/Cron container recreation; this is an operator
configuration concern, not a requirement to use a particular deployment overlay.

For a course transfer to another Moodle, restore as a new course rather than onto
the site home. Exclude enrolled users when only teaching content is required.
