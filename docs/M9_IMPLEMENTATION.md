# M9 implementation: Mermaid diagrams

Status: implemented on `codex/math-mermaid`

Target: LessonMark 0.2.0-alpha4

Date: 2026-09-07

## Delivered

- Mermaid fenced blocks remain canonical Markdown.
- Mermaid 11.17.2 and its MIT license are bundled locally.
- Preview and student display invoke the same strict browser renderer.
- Student pages load the 3.5 MB Mermaid payload only when required.
- The editor loads Mermaid so newly typed unsaved diagrams can be previewed.
- `securityLevel: strict`, disabled HTML labels, 50,000-character text limit,
  and 500-edge limit are applied.
- Successful SVG is responsive; failure leaves the original source visible.
- PDF export deliberately retains readable Mermaid source instead of persisting
  browser-generated SVG.

## Verification

- PHP unit coverage preserves Mermaid markers without unsupported-code
  diagnostics.
- Chrome Behat assertions cover SVG rendering in Preview and student display.
- Existing mathematics, self-check, Markdown, File API, backup/restore, and PDF
  tests remain part of the same quality gates.
