# M8 implementation: mathematics

Status: implemented on `codex/math-mermaid`

Target: LessonMark 0.2.0-alpha3

Date: 2026-09-07

## Delivered

- Inline LaTeX using `math:` or the compatibility alias `latex:`.
- Inline AsciiMath using `asciimath:`.
- Display formulas using `math`, `latex`, or `asciimath` fences.
- Local KaTeX 0.18.5 and asciimath-parser 0.6.11 browser rendering.
- Visual HTML plus MathML, responsive overflow, and Copy LaTeX controls.
- Strict, untrusted and bounded rendering with source-visible failure behavior.
- Shared author Preview and student-view behavior.
- Source-preserving import, export, backup, restore, and PDF fallback.

The browser-rendered result is disposable. `markdownsource` remains unchanged,
and the server-side PDF intentionally retains readable code when it cannot run
the browser renderer.

## Verification

- PHP lint: passed
- Moodle CodeSniffer with zero warnings: passed
- Moodle PHPDoc checker: passed
- Moodle plugin validation and savepoint checks: passed
- Moodle Grunt JavaScript/CSS checks: passed after committed AMD regeneration
- PHPUnit: 28 tests, 108 assertions passed
- Behat mathematics assertions: added for GitHub Actions/browser validation

M9 will add Mermaid without changing the mathematics source or rendering
contract.
