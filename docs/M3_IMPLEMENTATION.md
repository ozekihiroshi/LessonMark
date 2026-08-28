# M3 implementation result

## Outcome

M3 adds the teaching-document presentation layer without replacing Moodle's
Markdown parser or changing the stored source. Preview and student display both
use `moodle_markdown_renderer`, followed by one `teaching_document_enhancer`.

The enhancer operates only on Moodle-rendered, sanitised HTML and provides:

- Unicode-safe stable heading IDs with deterministic duplicate suffixes;
- a visible, labelled table of contents for documents with at least two headings;
- NOTE, TIP, and WARNING callouts with text, icon, colour, and semantic attributes;
- an allow-listed code-language mapping and diagnostics for unknown languages;
- accessible horizontal wrappers for wide tables;
- responsive teaching typography and print styles.

## Syntax highlighting dependency

Moodle 5.2 already ships PrismJS 1.29.0 through
`filter_codehighlighter/prism`. LessonMark reuses that AMD runtime and bundles
only the official PrismJS 1.29.0 Bash, JSON, and SQL components absent from the
core set needed by LessonMark.

The files, version, MIT licence, and upstream project are recorded in
`plugin/lessonmark/thirdpartylibs.xml`. The upstream licence is retained at
`plugin/lessonmark/vendor/prism/LICENSE`. `scripts/build-prism-languages.php`
checks fixed SHA-256 hashes before reproducibly generating the AMD source.
No CDN or runtime network request is used.

## Rendering boundary

```text
Markdown source
  -> source normalisation and raw-HTML neutralisation
  -> Moodle FORMAT_MARKDOWN
  -> Moodle clean_text
  -> teaching_document_enhancer
  -> Preview or student HTML
  -> local Prism highlighting in the browser
```

The final browser step adds only presentation spans. It does not alter the
source, persist HTML, or create a different server rendering path for Preview.
Only allow-listed `language-*` classes reach the highlighter.

## Automated coverage

The M3 PHPUnit fixtures cover:

- Unicode and duplicate headings, IDs, and TOC metadata;
- integrated Markdown rendering of TOC, callout, Python code, and table;
- all three consecutive callout types, including Moodle's merged-blockquote case;
- language aliases, unknown-language diagnostics, and table accessibility;
- preservation of Markdown blockquote markers by raw-HTML neutralisation.

The local CI remains the release gate: PHP lint, Moodle CodeSniffer, PHPDoc,
plugin validation, savepoints, JavaScript/CSS lint and AMD consistency, and
PHPUnit. Release ZIP construction still requires a clean committed worktree and
byte-identical output for the same commit.

## Manual test boundary

The UI-upload Moodle environment in `moodle-rescue` remains independent of the
LessonMark source tree. The install/upgrade and authenticated Preview/student
renderer fixture are tested against that environment after copying the release
candidate plugin into its running Moodle container. The fixture creates a real
course module, preserves Markdown in the database, and checks the resulting
TOC, heading IDs, all callouts, code languages, table wrapper, diagnostics, and
raw-HTML neutralisation.

The in-app browser cannot currently start against this Windows workspace
because its helper process is denied by the host ACL. This is an environment
limitation rather than a plugin failure. Guest login is disabled in the shared
UI test site, and it was deliberately not enabled globally merely for this
check. Therefore final rendered-pixel, client-side Prism token, and keyboard
inspection remain explicit work for the later accessibility/release gate; the
server DOM contract is covered automatically, while CI verifies the AMD build
artifacts.

## Next milestone

M4 adds images through Moodle's draft and permanent File API areas,
`@@PLUGINFILE@@` resolution, `pluginfile` access control, and unresolved-image
diagnostics. Relative-file bundle import remains outside M4.
