<?php
// Read-only inventory for the isolated 8095 upgrade test. No source text is printed.
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
if ($CFG->wwwroot !== 'http://localhost:8095') {
    throw new RuntimeException('This check is restricted to the 8095 laboratory.');
}
$items = [];
foreach ($DB->get_records('lessonmark', null, 'id ASC') as $item) {
    $cm = get_coursemodule_from_instance('lessonmark', $item->id, $item->course, false, MUST_EXIST);
    $context = context_module::instance($cm->id);
    $files = [];
    foreach (get_file_storage()->get_area_files($context->id, 'mod_lessonmark', 'content', false,
            'filepath, filename', false) as $file) {
        $files[] = [$file->get_filepath(), $file->get_filename(), $file->get_contenthash(),
            hash('sha256', $file->get_content())];
    }
    $items[] = [
        'id' => $item->id, 'courseid' => $item->course, 'cmid' => $cm->id,
        'recordsha256' => hash('sha256', json_encode($item)),
        'sourcebytes' => strlen($item->markdownsource),
        'sourcesha256' => hash('sha256', $item->markdownsource),
        'files' => $files,
    ];
}
if (!$items) {
    throw new RuntimeException('No LessonMark material exists; aborting empty test.');
}
echo json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
