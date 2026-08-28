# M4 implementation result

## Outcome

M4 adds Moodle-managed teaching images while keeping Markdown as the source of
truth. Images use Moodle's draft and permanent File API lifecycle; LessonMark
does not create or manage its own filesystem directory.

Teachers upload images in the activity form and reference them with the stable
source form:

```markdown
![Network topology](@@PLUGINFILE@@/images/network.png)
```

Preview rewrites the placeholder to the current user's `draftfile.php` URL.
After save, Preview and student display rewrite the same source to the module's
`pluginfile.php` URL. The placeholder, not either request URL, remains stored in
`markdownsource`.

## File API boundary

Permanent images use one fixed area:

```text
context:   course module
component: mod_lessonmark
filearea:  content
itemid:    0
```

The file manager accepts Moodle's `web_image` type group, permits subfolders,
and limits an activity to 50 files. Moodle site and course upload limits still
apply. Repository choices are restricted to internal copies; LessonMark does
not persist external aliases.

The `lessonmark_pluginfile()` callback rejects the wrong context or file area,
requires course login and `mod/lessonmark:view`, resolves only item ID 0, and
serves only a non-directory file found in the module area. Inline responses add
a restrictive Content Security Policy.

## Rendering and diagnostics

File URL rewriting is the last server-side presentation step after safe
Markdown rendering and teaching-document enhancement. Both Preview and student
display use the same renderer; only the target File API URL differs.

Preview reports:

- a missing alternative text diagnostic for an empty or absent image `alt`;
- an unresolved-relative-image diagnostic for references such as
  `images/example.png`.

M4 does not fetch relative paths, import adjacent files, or infer a filename.
A Markdown-and-images ZIP bundle remains a later feature.

## Automated and integration coverage

PHPUnit covers draft-to-permanent save, permanent-to-editing-draft preparation,
stored-file lookup, and both URL rewrite modes. Renderer tests cover canonical,
relative, external, and missing-alt image references. A module test generator
now creates LessonMark instances through Moodle's standard testing lifecycle.

`scripts/m4-smoke.php` was run against the existing disposable UI-upload Moodle
5.2 environment. It creates or reuses a real course module and verifies eight
contracts: source preservation, permanent save, student URL, editing-draft
restore, Preview URL, valid lookup, relative-path diagnostics, and alternative
text diagnostics.

## Current UX boundary

M4 deliberately uses Moodle's standard file manager beside the dedicated
Markdown editor. After upload, the author enters the documented
`@@PLUGINFILE@@` reference in Markdown. Automatic insertion at the caret would
require coupling to file-manager DOM details and is not part of this milestone.

## Next milestone

M5 adds one-time UTF-8 `.md` import and source export. Import does not create a
sync relationship and does not import adjacent relative images.
