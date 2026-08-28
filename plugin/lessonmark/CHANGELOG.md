# Change log

## 0.1.0-rc2 - 2026-08-29

- Removed the duplicate activity name from the student document body while
  retaining Moodle's standard activity header.
- Expanded the desktop Markdown authoring surface, enabled non-destructive soft
  wrapping, and reserved space above Moodle's fixed form actions.
- Allowed compact tables to fit their container without an unnecessary
  horizontal scrollbar while retaining overflow for genuinely wide content.

## 0.1.0-rc1 - 2026-08-28

- Added Markdown-first activity creation, editing, save-free preview, and
  student display through one rendering boundary.
- Added stable heading links, automatic contents, NOTE/TIP/WARNING callouts,
  syntax-highlighted code, responsive tables, and teaching typography.
- Added Moodle File API image management and author diagnostics.
- Added validated UTF-8 `.md` import and protected source export.
- Added backup, restore, course duplicate, and internal-link remapping.
- Added keyboard-operable responsive editor tabs, browser accessibility tests,
  security and privacy regression tests, expanded CI, and release verification.

This is a release candidate for Moodle 5.2 on PHP 8.3 and 8.4.
