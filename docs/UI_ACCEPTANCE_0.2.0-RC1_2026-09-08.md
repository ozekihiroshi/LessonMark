# 0.2.0-rc1 local UI acceptance — 2026-09-08

## Environment and candidate

- Existing WSL Ubuntu-24.04 Docker UI-upload environment, http://localhost:8085.
- Moodle 5.2.2 (20260810), PHP 8.3.33, MariaDB 11.8.8.
- Candidate: 0.2.0-rc1, build 2026090801.
- ZIP: `build/mod_lessonmark-0.2.0-rc1.zip`.
- SHA-256: `694ca5357d4074d7169981a043d608f833f0bbfb4b3f97c75cfba392d45f605b`.
- This candidate supersedes the September 7 artifact in RELEASE_EVIDENCE_0.2.0-RC1.md; do not combine their artifact identities.

## Deployment and upgrade

Browser automation could open the Moodle ZIP picker, but setting the file left
the input's file list empty. Consequently **UI ZIP upload was not verified**.
This does not establish a LessonMark upload defect.

After backing up the existing plugin and database, the verified ZIP was copied
into the existing web container and extracted into the plugin directory. The
old directory was moved aside, not deleted. No Docker volumes or source
repositories were deleted or recreated.

Moodle's web administration upgrade then showed LessonMark as the only plugin
requiring attention and successfully upgraded 2026083001 to 2026090801.

Local backups, excluded from distribution, are:

- `build/lessonmark-before-rc1-20260908.tar.gz`
- `build/lessonmark-before-rc1-20260908.sql` (sensitive database backup; do not commit)

## UI checks passed

A separate resource was created in test-1 / General without modifying the
existing teaching resource:

- Name: LessonMark 0.2.0-rc1 数式・Mermaid受入確認
- URL: http://localhost:8085/mod/lessonmark/view.php?id=32

Checks performed through the browser:

1. New Markdown resource creation and unsaved Preview.
2. Three inline expressions: `math:`, `latex:`, and `asciimath:`.
3. Fenced LaTeX quadratic formula and AsciiMath summation.
4. Mermaid flowchart with Japanese labels 原稿作成 → レビュー → 公開.
5. Ordinary Python fenced code remained code, with highlighting.
6. Save and display retained all five rendered formulas and the diagram.
7. Reopening Settings returned Markdown exactly equal to the entered source.
8. Switching to Student rendered five KaTeX formulas and one Mermaid SVG,
   with no math/diagram error elements. A screenshot visually confirmed the
   fraction, square root, summation, and labeled flowchart.
9. Narrow viewport used Edit/Preview tabs. Returned to the normal admin role
   and left Edit mode off after testing.

Student validation used Moodle's Switch role feature, not an independent
student account; it is not a complete authorization test. Wide split-pane
layout was not revalidated in this narrow browser viewport.

## Remaining checks / observations

- Retry the exact candidate ZIP upload manually or once browser file selection
  works reliably. Deployment plus web upgrade is not an upload lifecycle pass.
- Environment checks reported Composer installed-data, router configuration,
  and HTTP warnings. These are separate from the successful plugin upgrade.
- This run did not repeat offline, PDF, import/export, images, backup/restore,
  duplicate, malformed-input, or uninstall lifecycle tests.
- Existing hosted CI evidence is separate from this local acceptance check.

## Follow-up portability and release checks

The follow-up used the installed candidate and Moodle APIs in the same UI test
container. It did not replace plugin code. A scratch test adapted from
`scripts/m6-smoke.php` is retained at `build/rc1-portability.php`.

### Passed: API-level portability (14 checks)

- Dedicated source course 12, activity 34; restored course 13, activity 36;
  duplicated activity 37. Existing user-authored courses were not modified.
- Course backup and restore into a new course preserved the exact Markdown
  containing LaTeX, AsciiMath, Mermaid Japanese labels and an image reference,
  allowing only the expected internal-link remapping.
- Activity duplication preserved the same source and display options.
- Restored image content hashes matched; duplicate image file existed.
- Self and course links remapped correctly after restore; the duplicate's
  self-link remapped correctly.
- Import normalization returned the same Markdown bytes. This is an API check,
  not a browser file download/upload round trip.

### Failed: Japanese code-block text in saved-content PDF

The installed `pdf_exporter::generate()` produced a valid 74,733-byte one-page
PDF from activity 32. The output was rendered with PDFium and visually inspected.
Japanese title, headings and prose were readable, and mathematical source was
retained as designed. However, Japanese labels inside the Mermaid source block
became `????` (原稿作成, レビュー, 公開). This is a release-blocking fidelity issue.
The PDF's code-block font is a likely cause, but the fix has not been implemented
or verified. Original Markdown remains intact.

Scratch evidence: `build/rc1-saved-content.pdf`, `build/rc1-pdf-0.png`, and
`build/rc1-pdf-check.php`. These are test artifacts, not release package content.

### Still pending in this follow-up

The browser session had expired and opened the login page. The user was asked
to log in; no credentials or security settings were changed.

- Malformed math/Mermaid: live Preview and saved-view fallback.
- Browser `.md` export/import round trip.
- Restored/duplicated resource browser rendering and image delivery.
- Wide split-pane editing, narrow responsive layout, and browser print.
- External-network-blocked/cold-load rendering (static local-asset design is
  not a substitute for this runtime test).
- Actual whole-course duplication workflow, beyond the course backup/restore
  and activity duplication API checks above.

The follow-up is therefore partial, not a completed release gate.

### Browser follow-up after user login

- Restored activity 36 and duplicated activity 37 each rendered three KaTeX
  formulas and one Mermaid SVG with Japanese labels. Each managed Diagram
  image completed loading with natural width 16; these fixtures use a blank
  16px SVG, so this verifies delivery rather than complex image appearance.
- On duplicate 37, malformed `\frac{` and `not a diagram` produced their
  respective error markers while retaining readable source and the following
  Japanese paragraph, in both unsaved Preview and saved display.
- The duplicate was returned to its original valid Markdown and saved after
  the malformed-source test. Existing user teaching materials were untouched.
- At 1440×900, the editor and Preview were side by side (source width 564px,
  Preview width 562px); the source label accounts for the 32px top offset.
- At 390×844, Edit/Preview tabs worked, formulas and diagram rendered, and
  document width did not exceed viewport width. The temporary viewport override
  was reset afterwards.
- `.md` download event timed out after clicking Export saved .md. Import .md
  did not provide a filechooser event either. Neither is recorded as a passed
  browser round trip or a confirmed plugin defect.
- The in-app browser did not expose a print dialog on Ctrl+P, and its published
  capabilities contain no external-network blocking or print-media emulation.
  Browser print and cold-load external-network-blocked rendering remain pending.

Next release actions: fix and retest PDF Japanese code-block output; complete
file-transfer, print, external-network-blocked, and whole-course-copy UI checks
in a browser/test runner that supports those operations. This run makes no
plugin code changes and does not assert that all release gates passed.

### PDF Japanese code-block fix

Subsequently fixed `pdf_exporter` to set TCPDF's default preformatted font to
the same bundled Japanese font used by the body (`kozminproregular`). TCPDF
otherwise changes pre blocks to Courier, losing Japanese glyphs. No dependency
or font download was added.

- Updated only the exporter in the UI test container for verification.
- Re-exported activity 32: one page, 74,652 bytes; PDFium visual inspection
  confirmed 原稿作成, レビュー and 公開 were readable with intact surrounding content.
- Added a PHPUnit regression that checks decompressed PDF streams for Japanese
  Mermaid labels and Python string content, not merely intermediate HTML.
- Equivalent integration assertions for レビュー, 公開, 合格 passed in the running
  Moodle container. Exporter PHP lint and git diff whitespace checks passed.
- Full PHPUnit/CI has not been rerun. Existing RC1 ZIP/release is unchanged;
  the test container now includes this additional working-tree fix.
- Git history shows the exporter was introduced in commit 2537798; the v0.1.0
  tag has no saved-content PDF exporter. This was not a v0.1.0 PDF regression.

Visual evidence: `build/rc1-fixed-content.pdf` and `build/rc1-fixed-0.png`.
