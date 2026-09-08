# Presentation mode — 0.2.0

Same saved Markdown, two views: continuous study material and classroom slides.
This is browser-rendered HTML, not a PDF viewer or PowerPoint exporter.

- Standalone `<!-- slide -->` at column zero separates slides, except inside
  backtick/tilde fences. Indented, quoted, inline or escaped examples remain text.
- Empty segments are ignored; an empty document still has one page. No marker
  means one slide. Normal display/Preview/PDF omit structural markers.
- Each slide is an independent Markdown block: close lists/fences and keep
  reference definitions within the slide. No automatic splitting or shrinking.
- Presentation inherits course login, module availability and view capability.
  Saved content only; no separate copies, public links or new storage.
- Presentation does not load or persist browser-local self-check answers, to
  avoid projecting a learner's saved work. Input labels and radio groups remain
  unique across pages. Ordinary study-view answer persistence is unchanged.
- Previous/next, left/right keys, first/last keys, page count, optional browser
  fullscreen and an always available return link. Inputs retain their arrow keys.
- Hide Moodle navigation and generated TOC. Long slides scroll. Narrow screens
  remain usable. Fullscreen denial must not prevent ordinary presentation.
- Reuse the safe renderer and local math/Mermaid/image assets. Invalid notation
  retains readable source. Do not execute arbitrary HTML.
- JavaScript failure falls back to all pages, with a return link. Print all slides.
- Excluded: presenter notes, animation, automatic pagination, PowerPoint export.

Gate: splitter tests, marker preservation in fences, view authorization, navigation,
fullscreen fallback, long/narrow slides, math/Mermaid, normal-view and PDF regression.
RC2 is not ready until these checks and the existing release gates pass.
