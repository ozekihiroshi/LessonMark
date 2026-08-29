PrismJS components bundled by LessonMark
==========================================

Upstream project: https://prismjs.com/
Upstream source: https://github.com/PrismJS/prism
Pinned version: 1.29.0
License: MIT (see LICENSE in this directory)

LessonMark vendors the upstream Bash, JSON, and SQL language components used by
its syntax highlighter. The component files in this directory are unmodified
upstream files:

- components/prism-bash.js
- components/prism-json.js
- components/prism-sql.js

To reconstruct the generated Moodle AMD source:

1. Download the PrismJS 1.29.0 source archive from the upstream repository.
2. Copy the three component files above and the upstream LICENSE into this
   directory.
3. From the LessonMark repository root, run:

       php scripts/build-prism-languages.php

   This writes plugin/lessonmark/amd/src/prism-languages.js with its provenance
   and MIT license header.
4. Use Moodle's normal Grunt AMD build to generate amd/build files, or run the
   repository CI/build process.

No network download, package manager, or build tool is required on a Moodle
production server. The release ZIP includes the generated AMD build.
