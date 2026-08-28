# M5 implementation result

## Outcome

M5 adds one-time `.md` import into the dedicated Markdown editor and download
of the source currently saved in Moodle. It does not introduce a second source
mode, synchronization relationship, or server-side import staging area.

## Import boundary

Import runs locally in the browser after the teacher selects a file whose name
ends in `.md`. Before replacing non-empty editor content, LessonMark displays a
Moodle standard confirmation dialog. The selected file is then:

- decoded as UTF-8 with malformed byte sequences rejected;
- stripped of one leading UTF-8 byte order mark;
- normalised from CRLF or CR to LF, matching browser textarea behaviour;
- limited to 512 KiB after normalisation;
- placed in the editor as an unsaved change.

The regular form validation checks UTF-8 and the source limit again when the
activity is saved. Import triggers the existing change checker and Preview
pipeline. It does not publish, upload adjacent images, or retain a connection
to the selected file. Canonical `@@PLUGINFILE@@` references remain unchanged;
relative image diagnostics continue to apply.

## Export boundary

`export.php` reads `markdownsource` from the saved LessonMark record. It does
not export unsaved text currently visible in an edit form. The endpoint:

- resolves a real LessonMark course module;
- requires course login;
- requires `mod/lessonmark:edit` in the module context;
- requires a valid sesskey;
- derives a safe `.md` filename with Moodle's filename sanitiser;
- uses Moodle's forced-download response helper with no caching.

The downloaded bytes are the stored Markdown source. No HTML rendering,
newline conversion, BOM insertion, or image bundling occurs during export.

## Automated and integration coverage

PHPUnit covers BOM removal, CRLF/CR normalisation, malformed UTF-8 rejection,
the 512 KiB boundary, and safe `.md` filename generation. Existing CRUD tests
continue to prove that saved Markdown remains unchanged.

`scripts/m5-smoke.php` was run against the existing disposable UI-upload
Moodle 5.2 environment. It creates or reuses a real course module and verifies
eight contracts: saved-source preservation, BOM removal, line-ending
normalisation, `@@PLUGINFILE@@` preservation, invalid UTF-8 rejection,
oversized-source rejection, safe export filename, and edit capability.

JavaScript/CSS lint and AMD reproducibility remain part of the local and GitHub
CI gates. Browser-pixel interaction remains outside this server-side smoke
script; the import uses Moodle's standard modal and file-input controls.

## Current boundary

M5 imports only one Markdown text file. ZIP bundles, adjacent relative images,
Git synchronization, automatic republishing, and export of image assets remain
outside v0.1.

## Next milestone

M6 adds Moodle backup/restore and course duplication for the Markdown source,
settings, and File API images.
