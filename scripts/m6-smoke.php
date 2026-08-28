<?php
/**
 * Exercises LessonMark backup, restore, and course duplication in a disposable Moodle environment.
 *
 * This script creates persistent smoke-test courses and is for development environments only.
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
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot . '/mod/lessonmark/lib.php');

\core\session\manager::set_user(get_admin());
$CFG->backup_file_logger_level = backup::LOG_NONE;
set_config('backup_general_users', 1, 'backup');

$shortname = 'LESSONMARK-M6';
$course = $DB->get_record('course', ['shortname' => $shortname]);
if (!$course) {
    $course = create_course((object) [
        'fullname' => 'LessonMark M6 smoke test',
        'shortname' => $shortname,
        'category' => 1,
        'format' => 'topics',
        'numsections' => 1,
        'visible' => 1,
    ]);
}

$name = 'M6 lifecycle fixture';
$lessonmark = $DB->get_record('lessonmark', ['course' => $course->id, 'name' => $name]);
if (!$lessonmark) {
    $module = $DB->get_record('modules', ['name' => 'lessonmark'], '*', MUST_EXIST);
    course_create_sections_if_missing($course, 1);
    $moduleinfo = add_moduleinfo((object) [
        'course' => $course->id,
        'module' => $module->id,
        'modulename' => 'lessonmark',
        'section' => 1,
        'name' => $name,
        'intro' => '<p>M6 integration fixture</p>',
        'introformat' => FORMAT_HTML,
        'markdownsource' => '# M6 lifecycle fixture',
        'displayoptions' => '{"toc":true}',
        'visible' => 1,
        'visibleoncoursepage' => 1,
        'cmidnumber' => '',
        'groupmode' => 0,
        'groupingid' => 0,
        'completion' => 0,
        'completionview' => 0,
        'completionexpected' => 0,
        'availabilityconditionsjson' => null,
    ], $course);
    $lessonmark = $DB->get_record('lessonmark', ['id' => $moduleinfo->instance], '*', MUST_EXIST);
}

$cm = get_coursemodule_from_instance('lessonmark', $lessonmark->id, $course->id, false, MUST_EXIST);
$source = implode("\n\n", [
    '# M6 lifecycle fixture',
    '![Diagram](@@PLUGINFILE@@/images/diagram.svg)',
    '[This lesson](' . $CFG->wwwroot . '/mod/lessonmark/view.php?id=' . $cm->id . ')',
    '[Course lessons](' . $CFG->wwwroot . '/mod/lessonmark/index.php?id=' . $course->id . ')',
]);
$lessonmark->markdownsource = $source;
$lessonmark->displayoptions = '{"toc":true}';
$DB->update_record('lessonmark', $lessonmark);

$fs = get_file_storage();
$context = context_module::instance($cm->id);
if (!$fs->file_exists($context->id, 'mod_lessonmark', 'content', 0, '/images/', 'diagram.svg')) {
    $fs->create_file_from_string([
        'contextid' => $context->id,
        'component' => 'mod_lessonmark',
        'filearea' => 'content',
        'itemid' => 0,
        'filepath' => '/images/',
        'filename' => 'diagram.svg',
        'mimetype' => 'image/svg+xml',
    ], '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"></svg>');
}

$backupcontroller = new backup_controller(
    backup::TYPE_1COURSE,
    $course->id,
    backup::FORMAT_MOODLE,
    backup::INTERACTIVE_NO,
    backup::MODE_IMPORT,
    get_admin()->id
);
$backupid = $backupcontroller->get_backupid();
$backupcontroller->execute_plan();
$backupcontroller->destroy();

$restoreshortname = 'LESSONMARK-M6-R-' . time();
$newcourseid = restore_dbops::create_new_course(
    'LessonMark M6 restored smoke test',
    $restoreshortname,
    $course->category
);
$restorecontroller = new restore_controller(
    $backupid,
    $newcourseid,
    backup::INTERACTIVE_NO,
    backup::MODE_GENERAL,
    get_admin()->id,
    backup::TARGET_NEW_COURSE
);
$precheck = $restorecontroller->execute_precheck();
if ($precheck !== true) {
    fwrite(STDERR, json_encode(['restore_precheck' => $precheck], JSON_PRETTY_PRINT) . "\n");
    exit(1);
}
$restorecontroller->execute_plan();
$restorecontroller->destroy();

$restored = $DB->get_record('lessonmark', ['course' => $newcourseid, 'name' => $name], '*', MUST_EXIST);
$restoredcm = get_coursemodule_from_instance('lessonmark', $restored->id, $newcourseid, false, MUST_EXIST);
$restoredcontext = context_module::instance($restoredcm->id);
$restoredfile = $fs->get_file(
    $restoredcontext->id,
    'mod_lessonmark',
    'content',
    0,
    '/images/',
    'diagram.svg'
);

$duplicatecm = \core_courseformat\formatactions::cm($course)->duplicate($cm->id);
$duplicate = $DB->get_record('lessonmark', ['id' => $duplicatecm->instance], '*', MUST_EXIST);
$duplicatecontext = context_module::instance($duplicatecm->id);
$duplicatefile = $fs->get_file(
    $duplicatecontext->id,
    'mod_lessonmark',
    'content',
    0,
    '/images/',
    'diagram.svg'
);

$checks = [
    'backup_feature_enabled' => lessonmark_supports(FEATURE_BACKUP_MOODLE2) === true,
    'restore_source_preserved' => str_contains($restored->markdownsource, '@@PLUGINFILE@@/images/diagram.svg'),
    'restore_self_link_remapped' => str_contains(
        $restored->markdownsource,
        $CFG->wwwroot . '/mod/lessonmark/view.php?id=' . $restoredcm->id
    ),
    'restore_course_link_remapped' => str_contains(
        $restored->markdownsource,
        $CFG->wwwroot . '/mod/lessonmark/index.php?id=' . $newcourseid
    ),
    'restore_displayoptions_preserved' => $restored->displayoptions === $lessonmark->displayoptions,
    'restore_content_file_preserved' => $restoredfile instanceof stored_file,
    'duplicate_source_preserved' => str_contains($duplicate->markdownsource, '@@PLUGINFILE@@/images/diagram.svg'),
    'duplicate_self_link_remapped' => str_contains(
        $duplicate->markdownsource,
        $CFG->wwwroot . '/mod/lessonmark/view.php?id=' . $duplicatecm->id
    ),
    'duplicate_displayoptions_preserved' => $duplicate->displayoptions === $lessonmark->displayoptions,
    'duplicate_content_file_preserved' => $duplicatefile instanceof stored_file,
];

$result = [
    'sourcecourseid' => (int) $course->id,
    'sourcecmid' => (int) $cm->id,
    'restoredcourseid' => (int) $newcourseid,
    'restoredcmid' => (int) $restoredcm->id,
    'duplicatecmid' => (int) $duplicatecm->id,
    'checks' => $checks,
];
if (in_array(false, $checks, true)) {
    fwrite(STDERR, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    exit(1);
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
