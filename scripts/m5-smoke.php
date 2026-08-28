<?php
/**
 * Creates and verifies the LessonMark M5 source-transfer fixture.
 *
 * This script is for disposable development environments only.
 *
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

$configpath = getenv('MOODLE_CONFIG') ?: '/var/www/html/config.php';
if (!is_file($configpath)) {
    fwrite(STDERR, "Moodle config.php was not found.\n");
    exit(1);
}

require($configpath);
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot . '/mod/lessonmark/lib.php');

\core\session\manager::set_user(get_admin());

$shortname = 'LESSONMARK-M5';
$course = $DB->get_record('course', ['shortname' => $shortname]);
if (!$course) {
    $course = create_course((object) [
        'fullname' => 'LessonMark M5 smoke test',
        'shortname' => $shortname,
        'category' => 1,
        'format' => 'topics',
        'numsections' => 1,
        'visible' => 1,
    ]);
}

$source = "# Export source\n\n![Diagram](@@PLUGINFILE@@/diagram.png)\n";
$name = 'M5 source transfer fixture';
$lessonmark = $DB->get_record('lessonmark', ['course' => $course->id, 'name' => $name]);
if (!$lessonmark) {
    $module = $DB->get_record('modules', ['name' => 'lessonmark'], '*', MUST_EXIST);
    course_create_sections_if_missing($course, 1);
    $moduleinfo = (object) [
        'course' => $course->id,
        'module' => $module->id,
        'modulename' => 'lessonmark',
        'section' => 1,
        'name' => $name,
        'intro' => 'M5 integration fixture',
        'introformat' => FORMAT_HTML,
        'markdownsource' => $source,
        'visible' => 1,
        'visibleoncoursepage' => 1,
        'cmidnumber' => '',
        'groupmode' => 0,
        'groupingid' => 0,
        'completion' => 0,
        'completionview' => 0,
        'completionexpected' => 0,
        'availabilityconditionsjson' => null,
    ];
    $moduleinfo = add_moduleinfo($moduleinfo, $course);
    $lessonmark = $DB->get_record('lessonmark', ['id' => $moduleinfo->instance], '*', MUST_EXIST);
} else {
    $lessonmark->instance = $lessonmark->id;
    $lessonmark->markdownsource = $source;
    lessonmark_update_instance($lessonmark);
    $lessonmark = $DB->get_record('lessonmark', ['id' => $lessonmark->id], '*', MUST_EXIST);
}

$cm = get_coursemodule_from_instance('lessonmark', $lessonmark->id, $course->id, false, MUST_EXIST);
$imported = \mod_lessonmark\local\source_transfer::normalise_import(
    "\xEF\xBB\xBF# Imported\r\n\rBody\r\n![Diagram](@@PLUGINFILE@@/diagram.png)"
);
$invalidrejected = false;
try {
    \mod_lessonmark\local\source_transfer::normalise_import("Broken \xC3\x28");
} catch (invalid_parameter_exception $exception) {
    $invalidrejected = true;
}
$oversizerejected = false;
try {
    \mod_lessonmark\local\source_transfer::normalise_import(str_repeat(
        'x',
        \mod_lessonmark\local\moodle_markdown_renderer::MAX_SOURCE_BYTES + 1
    ));
} catch (invalid_parameter_exception $exception) {
    $oversizerejected = true;
}
$filename = \mod_lessonmark\local\source_transfer::export_filename('../../M5 fixture.md');
$checks = [
    'saved_source_preserved' => $lessonmark->markdownsource === $source,
    'bom_removed' => !str_starts_with($imported, "\xEF\xBB\xBF"),
    'line_endings_normalised' => !str_contains($imported, "\r"),
    'pluginfile_reference_preserved' => str_contains($imported, '@@PLUGINFILE@@/diagram.png'),
    'invalid_utf8_rejected' => $invalidrejected,
    'oversized_source_rejected' => $oversizerejected,
    'export_filename_safe' => str_ends_with($filename, '.md') && !str_contains($filename, '/'),
    'edit_capability_enforced' => has_capability('mod/lessonmark:edit', context_module::instance($cm->id)),
];

$result = [
    'courseid' => (int) $course->id,
    'cmid' => (int) $cm->id,
    'instanceid' => (int) $lessonmark->id,
    'filename' => $filename,
    'checks' => $checks,
];
if (in_array(false, $checks, true)) {
    fwrite(STDERR, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    exit(1);
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
