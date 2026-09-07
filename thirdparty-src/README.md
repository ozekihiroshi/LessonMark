# Rebuilding LessonMark browser assets

LessonMark renders mathematics and Mermaid diagrams entirely in the browser
without a CDN. The generated files are committed so Moodle servers do not need
Node.js. Maintainers can reproduce them from the pinned lockfile.

Requirements: Node.js 22 and npm 10.

```sh
npm ci
npm run build:assets
git diff --exit-code -- plugin/lessonmark/vendor
```

Inputs and outputs:

- `thirdparty-src/math-render-source.js` bundles KaTeX 0.18.5 and
  asciimath-parser 0.6.11 to `vendor/math/math-render.min.js`.
- `thirdparty-src/build-math.mjs` copies KaTeX CSS, fonts, and licenses.
- `thirdparty-src/build-mermaid.mjs` copies Mermaid 11.17.2 and the small
  strict-security adapter to `vendor/mermaid`.
- `package-lock.json` records all transitive packages and integrity hashes.

Dependency changes require `npm audit`, a clean generated-asset diff review,
PHPUnit and Chrome Behat, and a new plugin version. Generated SVG, HTML, MathML,
and converted LaTeX remain disposable output and are not stored as lesson data.
