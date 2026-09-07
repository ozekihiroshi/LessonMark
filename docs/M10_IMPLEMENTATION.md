# M10 implementation: release quality and portability

M10 turns the M8 mathematics and M9 Mermaid work into a reviewable 0.2 release
candidate. It does not change Markdown into generated HTML: source remains the
stored, exported, backed-up, and duplicated representation.

## Reproducible local browser assets

The repository now owns a pinned Node 22/npm build boundary:

- `package-lock.json` records exact direct and transitive packages with npm
  integrity data;
- `thirdparty-src/` contains the small LessonMark adapters and deterministic
  build/copy scripts;
- `npm run build:assets` reproduces committed KaTeX, AsciiMath, and Mermaid
  files without changing them; and
- Moodle release ZIPs contain generated assets and licenses, but never
  `node_modules`, package tooling, or development source.

The exact direct versions are KaTeX 0.18.5, asciimath-parser 0.6.11, Mermaid
11.17.2, and esbuild 0.28.2. `thirdpartylibs.xml`, bundled license files, the
lockfile, and `scripts/verify-release.php` form the attribution and integrity
boundary. Updating a dependency requires an explicit hash update and full M10
gates.

## Runtime portability

Formula and Mermaid assets are local and require no CDN, API, account, or
network connection. Student pages select them from fixed classes emitted by
the server renderer. Ordinary lessons do not load those large optional assets.
The same local assets operate in Preview and student display.

Rendering is progressive enhancement. Valid source gains accessible browser
output; invalid, too-large, or unsupported source keeps its readable fenced or
inline representation with an error marker. A saved-content PDF deliberately
retains formula and diagram source because its server-side TCPDF path does not
execute the browser renderer.

## Quality gates

Automated coverage includes:

- conditional asset selection without evaluating author text;
- valid LaTeX, AsciiMath, and Mermaid rendering in Preview and student display;
- readable malformed-source fallback;
- PDF source retention;
- Moodle PHP 8.3 and 8.4 CI, PHPUnit, Moodle coding checks, Grunt, and Chrome
  Behat accessibility checks;
- npm security audit and an exact generated-asset diff; and
- two byte-identical release builds plus required file and hash inspection.

The manual release gate uses the non-source-mounted Moodle UI upload
environment. It covers clean install or upgrade, editing and Preview, student
display, Offline browser behavior, import/export, managed images,
backup/restore, course duplicate, PDF, and uninstall/reinstall when storage
changes.

## Release boundary

M10 targets `0.2.0-rc1`, Moodle 5.2, and PHP 8.3 or 8.4. Production servers
need only the verified `mod_lessonmark.zip`; Composer and Node.js remain
development-only. Results tied to a commit, GitHub Actions run, upload test,
and ZIP SHA-256 belong in the release evidence before a public 0.2.0 tag.
