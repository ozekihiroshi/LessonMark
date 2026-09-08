<?php
// Read-only rendering checks for the isolated upgrade lab.
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
require_once($CFG->libdir . '/filelib.php');
if ($CFG->wwwroot !== 'http://localhost:8095' || get_config('mod_lessonmark', 'version') != 2026090802) {
    throw new RuntimeException('Wrong site or plugin version.');
}
\core\session\manager::set_user(get_admin());
$checked = 0;
foreach ($DB->get_records('lessonmark', null, 'id ASC') as $item) {
    $cm = get_coursemodule_from_instance('lessonmark', $item->id, $item->course, false, MUST_EXIST);
    $context = context_module::instance($cm->id);
    $course = get_course($item->course);
    $renderer = new \mod_lessonmark\local\moodle_markdown_renderer();
    $renderer->render($item->markdownsource, $context)->get_content_html();
    foreach (\mod_lessonmark\local\presentation_source::split($item->markdownsource) as $slide) {
        $renderer->render($slide, $context)->get_content_html();
    }
    $pdf = (new \mod_lessonmark\local\pdf_exporter())->generate($item, $course, $context);
    if (!str_starts_with($pdf, '%PDF-')) {
        throw new RuntimeException('Invalid PDF for activity ' . $cm->id);
    }
    $checked++;
}
echo "Server-side HTML, slides and PDF generation passed for {$checked} lessons.\n";
