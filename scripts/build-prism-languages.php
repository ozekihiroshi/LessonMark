<?php
/**
 * Builds the LessonMark Prism language AMD source from pinned upstream files.
 *
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (PHP_SAPI !== 'cli') {
    exit(1);
}

$root = dirname(__DIR__);
$components = [
    'prism-bash.js' => '6c67db1a4c86269dc754b588d0ad3a0cdb295044fd466ea6f66bbf01dec306bd',
    'prism-json.js' => '835c44857c3f295f2c5bd70316006e455779da76287b0e14d93bbc995f658e4b',
    'prism-sql.js' => 'c208fdd212ff69c123c252290d5c325375bfefb0f6c26523b463909606cf3567',
];
$source = <<<'JS'
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Prism 1.29.0 language additions required by LessonMark.
 *
 * The language definitions between the eslint markers are generated verbatim
 * from the pinned upstream files in vendor/prism/components.
 *
 * @module     mod_lessonmark/prism-languages
 * @copyright  2012 Lea Verou
 * @license    https://opensource.org/license/mit MIT
 */

import Prism from 'filter_codehighlighter/prism';

/* eslint-disable */
JS;

foreach ($components as $filename => $expectedhash) {
    $path = $root . '/plugin/lessonmark/vendor/prism/components/' . $filename;
    $actualhash = hash_file('sha256', $path);
    if ($actualhash === false || !hash_equals($expectedhash, $actualhash)) {
        fwrite(STDERR, "Prism component checksum mismatch: {$filename}\n");
        exit(1);
    }
    $component = file_get_contents($path);
    if ($component === false) {
        fwrite(STDERR, "Unable to read Prism component: {$filename}\n");
        exit(1);
    }
    $source .= "\n" . rtrim(str_replace("\r\n", "\n", $component)) . "\n";
}
$source .= <<<'JS'
/* eslint-enable */

export default Prism;
JS;
$source .= "\n";

$output = $root . '/plugin/lessonmark/amd/src/prism-languages.js';
if (!is_dir(dirname($output))) {
    mkdir(dirname($output), 0777, true);
}
if (file_put_contents($output, $source) === false) {
    fwrite(STDERR, "Unable to write generated Prism language module.\n");
    exit(1);
}
echo "Generated {$output}\n";
