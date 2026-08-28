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
 * Backup structure for LessonMark activities.
 *
 * @package   mod_lessonmark
 * @category  backup
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Defines the complete LessonMark activity structure.
 */
class backup_lessonmark_activity_structure_step extends backup_activity_structure_step {
    /**
     * Defines record fields and File API areas included in backup.
     *
     * @return backup_nested_element Prepared activity structure.
     */
    protected function define_structure(): backup_nested_element {
        $lessonmark = new backup_nested_element('lessonmark', ['id'], [
            'name',
            'intro',
            'introformat',
            'markdownsource',
            'displayoptions',
            'timecreated',
            'timemodified',
        ]);
        $lessonmark->set_source_table('lessonmark', ['id' => backup::VAR_ACTIVITYID]);
        $lessonmark->annotate_files('mod_lessonmark', 'intro', null);
        $lessonmark->annotate_files('mod_lessonmark', 'content', null);

        return $this->prepare_activity_structure($lessonmark);
    }
}
