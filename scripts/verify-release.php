<?php
/**
 * Verifies LessonMark release metadata without loading Moodle.
 *
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (PHP_SAPI !== 'cli') {
    exit(1);
}
$expected = $argv[1] ?? '';
if (!preg_match('/^\d+\.\d+\.\d+(?:-[0-9A-Za-z.-]+)?$/D', $expected)) {
    fwrite(STDERR, "Expected a semantic release version.\n");
    exit(1);
}
$contents = file_get_contents(dirname(__DIR__) . '/plugin/lessonmark/version.php');
if ($contents === false) {
    fwrite(STDERR, "Unable to read plugin version.php.\n");
    exit(1);
}
$checks = [
    '/\$plugin->component\s*=\s*\'mod_lessonmark\';/' => 'component',
    '/\$plugin->version\s*=\s*\d{10};/' => 'build number',
    '/\$plugin->requires\s*=\s*2026042000;/' => 'Moodle requirement',
    '/\$plugin->supported\s*=\s*\[502,\s*502\];/' => 'support range',
];
foreach ($checks as $pattern => $description) {
    if (preg_match($pattern, $contents) !== 1) {
        fwrite(STDERR, "Invalid {$description} in version.php.\n");
        exit(1);
    }
}
if (preg_match('/\$plugin->release\s*=\s*\'([^\']+)\';/', $contents, $matches) !== 1
        || !hash_equals($expected, $matches[1])) {
    fwrite(STDERR, "Release version does not match version.php.\n");
    exit(1);
}
echo "Verified mod_lessonmark release metadata for {$expected}.\n";

