# Course presentation — next version (development)

Published 0.2.0 remains unchanged. Work branch: `codex/course-presentation`.

## Acceptance scope

- Start from a LessonMark resource using **Course presentation**.
- Follow course section/module order through visible, course-listed LessonMark resources.
  Other activity types, hidden sections/modules and stealth modules are excluded.
  User-specific access restrictions and the LessonMark view capability still apply.
- Keep `<!-- slide -->` as the explicit boundary within a lesson. Last slide → Next
  loads the next lesson's first slide. Previous from the first slide loads the preceding
  lesson's last slide. Home/End remain within the current lesson.
- Keep the course shell, lesson selector and fullscreen session while switching lessons.
  Show both lesson position and slide position. At course boundaries disable navigation.
- Load only the current lesson, using the existing authenticated presentation endpoint.
  Do not preload/mark unseen lessons as viewed or copy/save source into another model.
- Navigation must not bypass a subsequently changed access restriction. A blocked, deleted
  or failed lesson displays an error; never skip it silently. Selection/return remain available.
- Existing single-lesson presentation, normal view, editing and printing stay available.

## Initial visual direction

White teaching canvas; quiet grey course header; compact labelled lesson selector,
Previous/Next, fullscreen and positions; no Moodle drawers or menus. Long slides scroll
without hiding the course controls. No automatic shrinking of overflowing text.
The selector must work by keyboard. Small screens may wrap the toolbar.

## Architecture and security

A same-origin iframe hosts the existing single-lesson view inside a persistent shell.
Every lesson request performs Moodle login, availability and capability checks again.
The shell playlist uses `get_fast_modinfo`; only names/URLs, not all source, are loaded.
Communication checks origin, source window, expected module ID and navigation token.
Only a successful child handshake enables slide controls. Math/Mermaid keep their existing
local rendering paths; no third-party presentation service is introduced.

Reference: [Moodle module visibility](https://moodledev.io/docs/5.1/apis/plugintypes/mod/visibility).

## Verification gates (not yet accepted)

1. Unit tests: course order, hidden/stealth/blocked/non-LessonMark exclusion.
2. Controller tests: lesson boundaries in both directions, last-slide return, direct selection,
   first/last bounds, stale/untrusted messages, load failure and fullscreen denial.
3. Moodle browser: multiple lessons with multiple slides, Japanese, math, Mermaid, images,
   fullscreen retained, keyboard/select/input behaviour, narrow/long slides.
4. Student restrictions and session expiry; no views recorded simply by listing the course.
5. Regression: individual presentation, original source, browser print and backup/restore.

Course-wide PDF printing, PowerPoint export, non-LessonMark activities, presenter notes,
animated transitions and saved presentation progress are not part of this iteration.
This development branch is not yet a release or an installed upgrade.

## Development check — 2026-09-12

- Added the course shell, authenticated per-lesson frame, playlist selector and navigation protocol.
- Passed `node scripts/test-presentation.cjs` and `node scripts/test-course-presentation.cjs`.
- PHP syntax checked in WSL; `git diff --check` passed.
- Added playlist PHPUnit tests and cross-lesson Behat scenario; **not executed yet**.
- Existing development server: `http://localhost:8083/`, container `moodle-rescue-local`.
  Its bind-mounted plugin code now reflects this development branch. Database upgrade has
  not been run. Browser reached its login page; authenticated visual testing remains pending.
- No new containers, AWS changes, changes to the 8095 teaching site, Git push or release upload.

### Fullscreen keyboard follow-up

- Owner confirmed basic course presentation and explicit slide splitting work.
- Fullscreen button completion now restores focus to the current slide, including exit
  and denied requests. Course mode uses the validated `focus` message without resetting position.
- The slide canvas focus ring is a quiet 1px border; controls and inputs retain their own indicators.
- Both Node controller suites passed, including focus restoration and untrusted-message rejection.
- Purged caches only on `moodle-rescue-local` (8083). Actual fullscreen/visual retest remains pending.

### Teaching typography follow-up

- Owner confirmed immediate arrow navigation in fullscreen and up/down scrolling.
- Added screen-only typography: white canvas, dark grey text, muted blue headings/links,
  narrower reading measure, pale code blocks and table headers, alternating table rows.
- Presentation headings and code now scale with its body font; controls and narrow-screen
  slide padding stay compact. Source, renderer, navigation and print styles were not rewritten.
- Both Node controller suites and whitespace checks passed. Browser fixture visually checked
  Japanese text, code, callout and table in document/presentation layouts using the actual CSS.
- Purged caches on 8083 only. Actual Moodle theme integration, mobile and print regression
  checks remain pending; the browser available to the agent was at the login page.

### Actual-course PDF follow-up — 2026-09-12

- Course 19, activity 437 on 8083 was checked at 390px and 768px widths.
  Course controls remained available, five page images loaded, and Next/Right changed slides.
- The supplied PDF was a TCPDF export, not browser printing. The visible marker was a
  source typo (`<!- slide -->` at line 93), not an unhandled valid slide separator.
  With explicit owner approval, only that typo was corrected; a pre-edit source backup was kept.
- PDF image paragraphs now stay with adjacent headings using TCPDF's `nobr` grouping.
  Raster dimensions are explicitly bounded to 170mm wide / 210mm high without upscaling,
  reserving heading space. Nested image-level `page-break-inside:avoid` was removed because
  it interfered with TCPDF's outer no-break transaction.
- Short answer/response blocks stay together; long answers remain breakable.
- Regenerated the 11-page real-course PDF and visually inspected all pages. The five
  booklet headings now share pages with their images; the malformed marker is gone.
  PHP syntax and real-course HTML assertions passed. PHPUnit regression assertions were
  added, but the updated PHPUnit/CI suite has not yet been run.
- Browser printing and packaged ZIP upgrade acceptance remain pending. These changes
  are only in the 8083 source-mounted development environment, not AWS or the 8095 site.
