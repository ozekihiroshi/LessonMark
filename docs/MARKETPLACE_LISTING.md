# LessonMark public listing

## Name

LessonMark

## Short description

Author, preview, and publish accessible Moodle teaching resources while
keeping Markdown as the editable source of truth.

## Description

LessonMark is a Moodle course resource for technical and text-rich teaching
material. Teachers can create a new resource directly in Moodle or import a
`.md` file, edit Markdown beside a live preview, manage teaching images with
Moodle's File API, and publish the same sanitised rendering to students.

The plugin keeps Markdown—not generated HTML—as the editable source of truth.
This makes material practical to review, export, diff, translate, process with
authoring tools, and reuse outside one Moodle database.

Version 0.1.0 includes:

- responsive split Markdown editor and preview, with mobile Edit/Preview tabs;
- headings with stable IDs and an automatic table of contents;
- fenced code highlighting, tables, links, lists, and teaching typography;
- NOTE, TIP, and WARNING callouts;
- accessible Moodle-managed images using `@@PLUGINFILE@@`;
- validated UTF-8 `.md` import and capability-protected source export;
- shared safe rendering for preview and student display; and
- activity backup, restore, course duplicate, and internal-link remapping.

Raw HTML is not supported as authoring syntax. LessonMark does not contact an
external service, require a subscription, or transfer personal data outside
Moodle.

## Requirements

- Moodle 5.2
- PHP 8.3 or 8.4

## Useful links

- Source: https://github.com/ozekihiroshi/LessonMark
- Issues: https://github.com/ozekihiroshi/LessonMark/issues
- Security: https://github.com/ozekihiroshi/LessonMark/security/policy
- Installation: https://github.com/ozekihiroshi/LessonMark/blob/main/docs/INSTALLATION.md
- Authoring guide: https://github.com/ozekihiroshi/LessonMark/blob/main/docs/AUTHORING_GUIDE.md

## Suggested screenshots

1. `docs/screenshots/lessonmark-authoring.png`: Markdown editor and Preview.
2. `docs/screenshots/lessonmark-student-view.png`: published teaching page.
