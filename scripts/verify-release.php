<?php
/**
 * Verifies LessonMark release metadata and the installable ZIP.
 *
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (PHP_SAPI !== 'cli') {
    exit(1);
}

$repositoryroot = dirname(__DIR__);
$pluginroot = $repositoryroot . '/plugin/lessonmark';
$versioncontents = file_get_contents($pluginroot . '/version.php');
if ($versioncontents === false) {
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
    if (preg_match($pattern, $versioncontents) !== 1) {
        fwrite(STDERR, "Invalid {$description} in version.php.\n");
        exit(1);
    }
}
if (preg_match('/\$plugin->release\s*=\s*\'([^\']+)\';/', $versioncontents, $matches) !== 1) {
    fwrite(STDERR, "Unable to read the release version.\n");
    exit(1);
}
$actualrelease = $matches[1];
$expectedrelease = $argv[1] ?? $actualrelease;
if (!preg_match('/^\d+\.\d+\.\d+(?:-[0-9A-Za-z.-]+)?$/D', $expectedrelease)) {
    fwrite(STDERR, "Expected a semantic release version.\n");
    exit(1);
}
if (!hash_equals($expectedrelease, $actualrelease)) {
    fwrite(STDERR, "Release version does not match version.php.\n");
    exit(1);
}
$expectedmaturity = str_contains($actualrelease, '-') ? 'MATURITY_ALPHA' : 'MATURITY_STABLE';
if (preg_match('/\$plugin->maturity\s*=\s*' . $expectedmaturity . ';/', $versioncontents) !== 1) {
    fwrite(STDERR, "Release maturity does not match the semantic version.\n");
    exit(1);
}

$requiredfiles = [
    'README.md',
    'CHANGELOG.md',
    'readme_moodle.txt',
    'thirdpartylibs.xml',
    'vendor/prism/LICENSE',
    'amd/build/editor.min.js',
    'amd/build/prism-languages.min.js',
    'amd/build/self-check.min.js',
    'vendor/prism/readme_moodle.txt',
    'amd/build/syntax-highlighter.min.js',
];
foreach ($requiredfiles as $relativepath) {
    if (!is_file($pluginroot . '/' . $relativepath)) {
        fwrite(STDERR, "Required release file is missing: {$relativepath}\n");
        exit(1);
    }
}

$zippath = $argv[2] ?? $repositoryroot . '/build/mod_lessonmark.zip';
if (is_file($zippath)) {
    if (!class_exists(ZipArchive::class)) {
        fwrite(STDERR, "The ZIP extension is required to inspect the release artifact.\n");
        exit(1);
    }
    $zip = new ZipArchive();
    if ($zip->open($zippath) !== true) {
        fwrite(STDERR, "Unable to open the release ZIP.\n");
        exit(1);
    }
    $entries = [];
    for ($index = 0; $index < $zip->numFiles; $index++) {
        $entry = $zip->getNameIndex($index);
        if ($entry === false) {
            $zip->close();
            fwrite(STDERR, "Unable to inspect a release ZIP entry.\n");
            exit(1);
        }
        if (!str_starts_with($entry, 'lessonmark/')
                || str_contains($entry, '../')
                || preg_match('#(^|/)(?:\.git|node_modules|vendor/bin)(?:/|$)#', $entry) === 1) {
            $zip->close();
            fwrite(STDERR, "Unsafe or development-only ZIP entry: {$entry}\n");
            exit(1);
        }
        $entries[$entry] = true;
    }
    $zip->close();
    foreach ($requiredfiles as $relativepath) {
        if (!isset($entries['lessonmark/' . $relativepath])) {
            fwrite(STDERR, "Required file is absent from the release ZIP: {$relativepath}\n");
            exit(1);
        }
    }
}

echo "Verified mod_lessonmark {$actualrelease} release metadata and package contents.\n";
