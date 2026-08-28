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
 * Restore task for one LessonMark activity.
 *
 * @package   mod_lessonmark
 * @category  backup
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/lessonmark/backup/moodle2/restore_lessonmark_stepslib.php');

/**
 * Provides the settings, steps, and link rules for LessonMark restore.
 */
class restore_lessonmark_activity_task extends restore_activity_task {
    /**
     * LessonMark has no activity-specific restore settings.
     */
    protected function define_my_settings(): void {
    }

    /**
     * Adds the LessonMark XML restore step.
     */
    protected function define_my_steps(): void {
        $this->add_step(new restore_lessonmark_activity_structure_step(
            'lessonmark_structure',
            'lessonmark.xml'
        ));
    }

    /**
     * Defines fields whose encoded links must be decoded.
     *
     * @return restore_decode_content[] Decode content definitions.
     */
    public static function define_decode_contents(): array {
        return [new restore_decode_content(
            'lessonmark',
            ['intro', 'markdownsource'],
            'lessonmark'
        )];
    }

    /**
     * Defines URL token mappings for restored LessonMark links.
     *
     * @return restore_decode_rule[] Decode rules.
     */
    public static function define_decode_rules(): array {
        return [
            new restore_decode_rule('LESSONMARKVIEWBYID', '/mod/lessonmark/view.php?id=$1', 'course_module'),
            new restore_decode_rule('LESSONMARKVIEWBYINSTANCE', '/mod/lessonmark/view.php?n=$1', 'lessonmark'),
            new restore_decode_rule('LESSONMARKINDEX', '/mod/lessonmark/index.php?id=$1', 'course'),
        ];
    }

    /**
     * Defines legacy activity log mappings.
     *
     * @return restore_log_rule[] Log rules.
     */
    public static function define_restore_log_rules(): array {
        return [
            new restore_log_rule('lessonmark', 'add', 'view.php?id={course_module}', '{lessonmark}'),
            new restore_log_rule('lessonmark', 'update', 'view.php?id={course_module}', '{lessonmark}'),
            new restore_log_rule('lessonmark', 'view', 'view.php?id={course_module}', '{lessonmark}'),
        ];
    }

    /**
     * Defines legacy course-level log mappings.
     *
     * @return restore_log_rule[] Course log rules.
     */
    public static function define_restore_log_rules_for_course(): array {
        return [new restore_log_rule(
            'lessonmark',
            'view all',
            'index.php?id={course}',
            null
        )];
    }
}
