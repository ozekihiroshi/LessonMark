# Security policy

LessonMark treats Markdown as untrusted author-controlled input. Preview and
student display use the same renderer and Moodle cleaning boundary; raw HTML is
not an authoring feature. File delivery is capability-checked through Moodle's
File API.

## Supported release

Security fixes are currently prepared for the latest stable `0.1.x` release
on Moodle 5.2 with PHP 8.3 or 8.4. Moodle 5.3 development builds may be tested
for early compatibility, but are not a supported production target until
Moodle 5.3 is released and the compatibility gate is completed.

## Reporting a vulnerability

Use [GitHub private vulnerability reporting](https://github.com/ozekihiroshi/LessonMark/security/advisories/new).
If that channel is unavailable, contact the repository owner privately through
their GitHub profile. Do not publish exploit details, credentials, private
course content, or student data in a public issue.

Include the LessonMark version, Moodle and PHP versions, affected capability or
role, reproduction steps, impact, and any proposed remediation. The maintainer
will acknowledge the report, reproduce it in an isolated environment, and
coordinate a fix and disclosure.

## Deployment boundary

Only install ZIP files built from a reviewed commit. Verify the SHA-256 digest,
back up Moodle and `moodledata`, test upgrades in a non-production environment,
and keep Moodle cron, HTTPS, permissions, and supported security updates in
place. No Composer or Node.js installation is required on a Moodle server.
