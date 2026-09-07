# LessonMark 0.2.0 publication audit

Status: release-candidate gates defined; exact commit, hosted CI, UI upload, and
artifact digest are recorded after the candidate is committed and tested.

## Product and data ownership

- Markdown remains the canonical stored source.
- Browser-generated KaTeX HTML/MathML and Mermaid SVG are disposable and are
  not saved, exported, backed up, or graded.
- Self-check working answers remain browser-local and are not exported to PDF.
- No external rendering service, analytics endpoint, subscription, or CDN is
  required.

## Third-party and supply-chain record

- KaTeX 0.18.5 (MIT), asciimath-parser 0.6.11 (MIT), Mermaid 11.17.2
  (MIT), and build-only esbuild 0.28.2 are pinned in `package-lock.json`.
- License and attribution files are included in the plugin ZIP and declared in
  `thirdpartylibs.xml`.
- `npm ci && npm audit --audit-level=high && npm run build:assets` must succeed
  and leave no vendor diff.
- The release verifier checks exact SHA-256 hashes for executable and style
  browser assets in both the tree and ZIP.

## Compatibility and accessibility

- Supported matrix: Moodle 5.2 with PHP 8.3 and PHP 8.4.
- Chrome Behat checks author Preview, student display, malformed input,
  keyboard behavior, and automated accessibility rules.
- Formula output provides visual HTML plus MathML; copy controls are keyboard
  operable. Mermaid SVG receives an accessible label.
- Narrow layouts use responsive editor tabs and content rules. Print/PDF
  retains readable source when browser execution is unavailable.

## Portability lifecycle

Before public 0.2.0, record evidence for:

- `.md` import and export preserving formula and Mermaid source;
- activity backup/restore and course duplicate preserving source and managed
  images;
- saved PDF retaining formula and diagram source;
- a clean upload or upgrade in the non-source-mounted Moodle environment;
- Offline Preview and student display with no CDN requests; and
- deterministic ZIP rebuild and SHA-256 digest.

## Deferred claims

Moodle versions other than 5.2, PHP versions outside 8.3/8.4, arbitrary raw
HTML, server-rendered SVG/MathML in PDF, Git synchronization, and graded
assessment are not claimed by this release.
