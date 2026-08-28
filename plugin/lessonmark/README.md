# LessonMark for Moodle

LessonMark is a Moodle activity module for creating and publishing teaching material whose source of truth remains Markdown.

Install this directory at `<moodle-root>/mod/lessonmark` or install the release ZIP through Moodle's plugin installer.

LessonMark currently provides:

- Markdown source storage without conversion to saved HTML;
- a split Markdown editor and save-free live preview;
- one-time UTF-8 `.md` import with overwrite protection;
- access-controlled export of the source saved in Moodle;
- the same safe rendering pipeline for preview and student display;
- stable heading links and an automatic table of contents;
- NOTE, TIP, and WARNING callouts;
- fenced code highlighting for the documented language set;
- responsive tables and teaching-oriented screen and print typography;
- Moodle-managed teaching images with draft editing and access-controlled display;
- diagnostics for unresolved relative images and missing alternative text.

Upload teaching images with the activity's file manager and reference them as
`![Alternative text](@@PLUGINFILE@@/image.png)`. Subfolders are supported.

Import places Markdown in the editor as an unsaved change. Export downloads the
source already saved in Moodle; it does not include unsaved edits or images.

See `docs/AUTHORING_GUIDE.md` in the source repository for the supported authoring syntax.

## Requirements

- Moodle 5.2
- PHP 8.3 or 8.4

## License

GNU GPL v3 or later.
