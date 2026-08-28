<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Lists LessonMark resources in a course.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
$courseid = required_param('id', PARAM_INT);
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
require_course_login($course);
$PAGE->set_url('/mod/lessonmark/index.php', ['id' => $course->id]);
$PAGE->set_title(get_string('modulenameplural', 'mod_lessonmark'));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->navbar->add(get_string('modulenameplural', 'mod_lessonmark'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'mod_lessonmark'));
$instances = get_all_instances_in_course('lessonmark', $course);
if (!$instances) {
    echo $OUTPUT->notification(get_string('noinstances', 'mod_lessonmark'), 'info');
} else {
    $table = new html_table();
    $table->head = [get_string('name')];
    foreach ($instances as $instance) {
        $url = new moodle_url('/mod/lessonmark/view.php', ['id' => $instance->coursemodule]);
        $attributes = $instance->visible ? [] : ['class' => 'dimmed'];
        $table->data[] = [html_writer::link($url, format_string($instance->name), $attributes)];
    }
    echo html_writer::table($table);
}
echo $OUTPUT->footer();

