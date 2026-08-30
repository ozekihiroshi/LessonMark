<?php
// This file is part of Moodle - http://moodle.org/
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
 * Displays a LessonMark resource.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$id = optional_param('id', 0, PARAM_INT);
$instanceid = optional_param('n', 0, PARAM_INT);
if ($id) {
    $cm = get_coursemodule_from_id('lessonmark', $id, 0, false, MUST_EXIST);
    $lessonmark = $DB->get_record('lessonmark', ['id' => $cm->instance], '*', MUST_EXIST);
} else if ($instanceid) {
    $lessonmark = $DB->get_record('lessonmark', ['id' => $instanceid], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('lessonmark', $lessonmark->id, $lessonmark->course, false, MUST_EXIST);
} else {
    throw new moodle_exception('missingidandcmid', 'mod_lessonmark');
}

$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/lessonmark:view', $context);

$PAGE->set_url('/mod/lessonmark/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($lessonmark->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$completion = new completion_info($course);
$completion->set_module_viewed($cm);
$event = \mod_lessonmark\event\course_module_viewed::create([
    'objectid' => $lessonmark->id,
    'context' => $context,
]);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('lessonmark', $lessonmark);
$event->trigger();

$renderer = new \mod_lessonmark\local\moodle_markdown_renderer();
$document = $renderer->render((string) $lessonmark->markdownsource, $context);
$contenthtml = $document->get_content_html();

if (str_contains($contenthtml, 'language-')) {
    $PAGE->requires->js_call_amd('mod_lessonmark/syntax-highlighter', 'init', ['.mod_lessonmark-content']);
}
if (str_contains($contenthtml, 'data-self-check=')) {
    $PAGE->requires->js_call_amd('mod_lessonmark/self-check', 'init', [[
        'cmid' => (int) $cm->id,
        'userId' => (int) $USER->id,
    ]]);
}

echo $OUTPUT->header();
if (trim((string) $lessonmark->intro) !== '') {
    echo $OUTPUT->box(format_module_intro('lessonmark', $lessonmark, $cm->id), 'generalbox mod_introbox');
}
echo html_writer::div($contenthtml, 'mod_lessonmark-content');
echo $OUTPUT->footer();
