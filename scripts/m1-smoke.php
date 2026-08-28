<?php
/**
 * Creates and verifies a minimal LessonMark resource in a test Moodle site.
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

\core\session\manager::set_user(get_admin());

$shortname = 'LESSONMARK-M1';
$course = $DB->get_record('course', ['shortname' => $shortname]);
if (!$course) {
    $course = create_course((object) [
        'fullname' => 'LessonMark M1 smoke test',
        'shortname' => $shortname,
        'category' => 1,
        'format' => 'topics',
        'numsections' => 1,
        'visible' => 1,
    ]);
}

$name = 'Markdown source and safety';
$lessonmark = $DB->get_record('lessonmark', ['course' => $course->id, 'name' => $name]);
if (!$lessonmark) {
    $module = $DB->get_record('modules', ['name' => 'lessonmark'], '*', MUST_EXIST);
    course_create_sections_if_missing($course, 1);
    $source = <<<'MARKDOWN'
# LessonMark M1

This is **rendered Markdown**.

<script>window.lessonmarkUnsafe = true;</script>

Use `<section>` as inline code.
MARKDOWN;
    $moduleinfo = (object) [
        'course' => $course->id,
        'module' => $module->id,
        'modulename' => 'lessonmark',
        'section' => 1,
        'name' => $name,
        'intro' => 'M1 integration fixture',
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
}

$cm = get_coursemodule_from_instance('lessonmark', $lessonmark->id, $course->id, false, MUST_EXIST);
$context = context_module::instance($cm->id);
$renderer = new \mod_lessonmark\local\moodle_markdown_renderer();
$document = $renderer->render($lessonmark->markdownsource, $context);
$html = $document->get_content_html();

$checks = [
    'markdown_source_preserved' => str_contains($lessonmark->markdownsource, '**rendered Markdown**'),
    'heading_rendered' => str_contains($html, '<h1>LessonMark M1</h1>'),
    'strong_rendered' => str_contains($html, '<strong>rendered Markdown</strong>'),
    'script_not_executable' => !str_contains($html, '<script>'),
    'raw_html_visible_as_text' => str_contains($html, '&lt;script&gt;'),
    'inline_code_rendered' => str_contains($html, '<code>&lt;section&gt;</code>'),
];

if (in_array(false, $checks, true)) {
    fwrite(STDERR, json_encode($checks, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    exit(1);
}

echo json_encode([
    'courseid' => (int) $course->id,
    'cmid' => (int) $cm->id,
    'instanceid' => (int) $lessonmark->id,
    'checks' => $checks,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

