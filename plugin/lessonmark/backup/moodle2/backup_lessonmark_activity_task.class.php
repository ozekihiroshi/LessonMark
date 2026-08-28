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
 * Backup task for one LessonMark activity.
 *
 * @package   mod_lessonmark
 * @category  backup
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/lessonmark/backup/moodle2/backup_lessonmark_stepslib.php');

/**
 * Provides the steps to back up one LessonMark activity.
 */
class backup_lessonmark_activity_task extends backup_activity_task {
    /**
     * LessonMark has no activity-specific backup settings.
     */
    protected function define_my_settings(): void {
    }

    /**
     * Adds the LessonMark XML structure step.
     */
    protected function define_my_steps(): void {
        $this->add_step(new backup_lessonmark_activity_structure_step(
            'lessonmark_structure',
            'lessonmark.xml'
        ));
    }

    /**
     * Encodes links whose IDs must be mapped during restore or duplicate.
     *
     * @param string $content Markdown or HTML content.
     * @return string Content containing backup link tokens.
     */
    public static function encode_content_links($content): string {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, '/');
        $content = preg_replace(
            '/(' . $base . '\/mod\/lessonmark\/index\.php\?id=)([0-9]+)/',
            '$@LESSONMARKINDEX*$2@$',
            $content
        );
        $content = preg_replace(
            '/(' . $base . '\/mod\/lessonmark\/view\.php\?id=)([0-9]+)/',
            '$@LESSONMARKVIEWBYID*$2@$',
            $content
        );
        $content = preg_replace(
            '/(' . $base . '\/mod\/lessonmark\/view\.php\?n=)([0-9]+)/',
            '$@LESSONMARKVIEWBYINSTANCE*$2@$',
            $content
        );

        return $content;
    }
}
