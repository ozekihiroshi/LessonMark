# M6 implementation result

## Outcome

M6 integrates LessonMark with Moodle's standard backup, restore, and course
duplicate lifecycle. Markdown remains the source of truth after every
operation, while files remain in Moodle File API storage under the newly
created course-module context.

## Backup boundary

The activity backup records the complete LessonMark instance data needed to
reconstruct a teaching resource:

- name, description, and description format;
- Markdown source and display options;
- creation and modification timestamps;
- the standard `intro` file area;
- the LessonMark `content` file area used by teaching images.

Derived HTML is not backed up because it is not stored as authoritative data.
It is regenerated from the restored Markdown by the shared renderer.

## Restore and duplicate behaviour

Restore inserts a new LessonMark record for the destination course, registers
the old-to-new instance mapping with Moodle, and restores both File API areas
into the destination module context. The same implementation is used by
Moodle's activity duplicate operation.

Canonical `@@PLUGINFILE@@` references are preserved in Markdown. Moodle then
resolves them against the restored context at render time. Absolute LessonMark
links using the following forms are encoded during backup and decoded with the
new identifiers during restore:

- `mod/lessonmark/view.php?id=<course-module id>`;
- `mod/lessonmark/view.php?n=<instance id>`;
- `mod/lessonmark/index.php?id=<course id>`.

External URLs and ordinary Markdown links are not rewritten.

## Automated and integration coverage

PHPUnit performs both a complete course backup/restore and an activity
duplicate using Moodle's production backup APIs. The tests verify record
fields, canonical image references, content and intro files, cross-resource
links, course links, and duplicate self-links.

`scripts/m6-smoke.php` performs the same lifecycle against the existing
disposable UI-upload Moodle environment. It creates a dedicated source course,
restores a new timestamped course, duplicates the source activity, and reports
each contract as JSON. It does not modify unrelated courses or repositories.

## Current boundary

M6 backs up Moodle-managed LessonMark activities. It does not fetch external
Markdown, snapshot Git repositories, bundle unresolved relative assets, or
preserve a synchronization relationship with an import source.

## Next milestone

M7 closes release quality: broader compatibility, security and privacy review,
accessibility and browser interaction coverage, documentation, and a public
release candidate package.
