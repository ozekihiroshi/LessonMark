<?php
// Synthetic fixture, restricted to the recovery copy's dedicated database.
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot . '/mod/lessonmark/lib.php');
if ($CFG->dbhost !== 'lessonmark-recovery-check-db') {
    throw new RuntimeException('Refusing to modify a non-recovery database.');
}
\core\session\manager::set_user(get_admin());
$shortname = 'LESSONMARK-IMAGE-UPGRADE';
$mode = $argv[1] ?? 'check';
$course = $DB->get_record('course', ['shortname' => $shortname]);
if ($mode === 'create') {
    if (get_config('mod_lessonmark', 'version') != 2026083001 || $course) {
        throw new RuntimeException('Creation requires alpha2 and an absent fixture.');
    }
    $course = create_course((object) ['fullname' => 'Image upgrade verification',
        'shortname' => $shortname, 'category' => 1, 'format' => 'topics', 'numsections' => 1]);
    course_create_sections_if_missing($course, 1);
    $module = $DB->get_record('modules', ['name' => 'lessonmark'], '*', MUST_EXIST);
    $info = add_moduleinfo((object) [
        'course' => $course->id, 'module' => $module->id, 'modulename' => 'lessonmark',
        'section' => 1, 'name' => '画像保持テスト', 'intro' => '', 'introformat' => FORMAT_HTML,
        'markdownsource' => "# 画像保持テスト\n\n![Colour test](@@PLUGINFILE@@/images/test.png)\n\n"
            . "![日本語ファイル名](@@PLUGINFILE@@/images/確認.png)\n",
        'displayoptions' => '{"toc":true}', 'visible' => 1, 'visibleoncoursepage' => 1,
        'cmidnumber' => '', 'groupmode' => 0, 'groupingid' => 0, 'completion' => 0,
        'completionview' => 0, 'completionexpected' => 0, 'availabilityconditionsjson' => null,
    ], $course);
    $cm = get_coursemodule_from_instance('lessonmark', $info->instance, $course->id, false, MUST_EXIST);
    $context = context_module::instance($cm->id);
    $im = imagecreatetruecolor(80, 48);
    imagefilledrectangle($im, 0, 0, 39, 47, imagecolorallocate($im, 220, 30, 30));
    imagefilledrectangle($im, 40, 0, 79, 47, imagecolorallocate($im, 30, 60, 220));
    ob_start(); imagepng($im); $bytes = ob_get_clean(); imagedestroy($im);
    foreach (['test.png', '確認.png'] as $filename) {
        get_file_storage()->create_file_from_string([
            'contextid' => $context->id, 'component' => 'mod_lessonmark', 'filearea' => 'content',
            'itemid' => 0, 'filepath' => '/images/', 'filename' => $filename, 'mimetype' => 'image/png',
        ], $bytes);
    }
}
if (!$course) { throw new RuntimeException('Fixture missing.'); }
$item = $DB->get_record('lessonmark', ['course' => $course->id], '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('lessonmark', $item->id, $course->id, false, MUST_EXIST);
$context = context_module::instance($cm->id);
$html = (new \mod_lessonmark\local\moodle_markdown_renderer())->render($item->markdownsource, $context)->get_content_html();
if (substr_count($html, '<img') !== 2 || str_contains($html, '@@PLUGINFILE@@')) {
    throw new RuntimeException('Images were not rewritten to managed URLs.');
}
foreach (['test.png', '確認.png'] as $filename) {
    $file = get_file_storage()->get_file($context->id, 'mod_lessonmark', 'content', 0, '/images/', $filename);
    if (!$file || getimagesizefromstring($file->get_content())[0] !== 80) {
        throw new RuntimeException('Image content is missing or invalid.');
    }
    if (!str_contains(rawurldecode(html_entity_decode($html)), '/images/' . $filename)) {
        throw new RuntimeException('Image reference missing from rendered HTML.');
    }
}
$pdf = (new \mod_lessonmark\local\pdf_exporter())->generate($item, $course, $context);
if (!str_starts_with($pdf, '%PDF-') || !str_contains($pdf, '/Subtype /Image')) {
    throw new RuntimeException('Managed image not embedded in PDF.');
}
echo "Two managed PNGs (including Japanese filename), rendered references and PDF image embedding passed.\n";
