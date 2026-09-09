# Moodle Marketplace update: LessonMark 0.2.0

Status: GitHub publication complete. Marketplace version upload NOT yet submitted.
The browser requires the owner's Moodle account login.

## Existing submission

Open the existing LessonMark entry in Plugin dashboard (historical review ticket
MMR-188). Add a new version; do not submit a duplicate plugin. Check the actual
current review status after login; the historical ticket is not proof of approval.

## Version fields

- Plugin: LessonMark
- Component: mod_lessonmark
- Release: 0.2.0
- Build: 2026090903
- Maturity: Stable
- Supported Moodle: 5.2
- Supported PHP: 8.3, 8.4
- Repository: https://github.com/ozekihiroshi/LessonMark
- Branch: main
- Tag: v0.2.0
- Issues: https://github.com/ozekihiroshi/LessonMark/issues
- ZIP: https://github.com/ozekihiroshi/LessonMark/releases/download/v0.2.0/mod_lessonmark.zip
- SHA-256: dcf80506a618c6e4d54877ae40c377b1d582f5e4163f07b2ddd9ebbabb032293

## Release notes to paste

LessonMark 0.2.0 adds locally bundled LaTeX and AsciiMath formulas, Mermaid diagrams,
ungraded same-page practice blocks, saved-content PDF download, and classroom
presentation with keyboard navigation and fullscreen support.

Markdown remains the editable source of truth. No CDN, subscription or external
rendering service is required. PDF includes managed images and readable formula
and diagram source rather than browser-rendered graphics. Japanese code-block text
and the floating help-button overlay in browser print have been addressed.

Tested on Moodle 5.2 with PHP 8.3 and 8.4, including Chrome Behat and accessibility,
reproducible packaging, content-preserving upgrades, backup/recovery, cross-site
course restoration and owner-confirmed whole-course copy and printing.

Back up the site and use Moodle's normal plugin upgrade. Do not uninstall the old
version first: uninstalling removes LessonMark activities and managed files.

## Description

Use the current description in MARKETPLACE_LISTING.md if the existing listing
still describes only version 0.1.0. Preserve the existing screenshots unless an
updated screenshot is intentionally supplied.
