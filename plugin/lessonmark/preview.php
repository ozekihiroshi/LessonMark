<?php
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
 * Renders an unsaved LessonMark draft for an authorised editor.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');

require_sesskey();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new moodle_exception('invalidrequestmethod', 'error', '', 'POST');
}

$source = required_param('markdownsource', PARAM_RAW);
$cmid = optional_param('cmid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$draftitemid = optional_param('draftitemid', 0, PARAM_INT);

if ($cmid > 0) {
    $cm = get_coursemodule_from_id('lessonmark', $cmid, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    require_login($course, false, $cm);
    $context = context_module::instance($cm->id);
    require_capability('mod/lessonmark:edit', $context);
} else if ($courseid > 0) {
    $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    require_login($course);
    $context = context_course::instance($course->id);
    require_capability('mod/lessonmark:addinstance', $context);
} else {
    throw new moodle_exception('missingpreviewcontext', 'mod_lessonmark');
}

$renderer = new \mod_lessonmark\local\moodle_markdown_renderer(
    draftitemid: $draftitemid > 0 ? $draftitemid : null
);
$document = $renderer->render($source, $context);
$diagnostics = array_map(static function (array $diagnostic): array {
    $value = (string) ($diagnostic['language'] ?? $diagnostic['path'] ?? '');
    $diagnostic['message'] = match ($diagnostic['type'] ?? '') {
        'unsupportedlanguage' => get_string('diagnosticunsupportedlanguage', 'mod_lessonmark', $value),
        'unresolvedrelativeimage' => get_string('diagnosticrelativeimage', 'mod_lessonmark', $value),
        'missingimagealt' => get_string('diagnosticmissingalt', 'mod_lessonmark', $value),
        default => get_string('diagnosticrendering', 'mod_lessonmark'),
    };
    return $diagnostic;
}, $document->get_diagnostics());

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'html' => $document->get_content_html(),
    'toc' => $document->get_toc(),
    'diagnostics' => $diagnostics,
], JSON_THROW_ON_ERROR);
