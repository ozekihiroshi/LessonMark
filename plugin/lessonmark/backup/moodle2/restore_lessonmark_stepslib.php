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
 * Restore structure for LessonMark activities.
 *
 * @package   mod_lessonmark
 * @category  backup
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Restores one LessonMark record and its File API areas.
 */
class restore_lessonmark_activity_structure_step extends restore_activity_structure_step {
    /**
     * Defines the LessonMark XML path.
     *
     * @return restore_path_element[] Prepared restore paths.
     */
    protected function define_structure(): array {
        $paths = [new restore_path_element('lessonmark', '/activity/lessonmark')];
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Inserts the restored LessonMark record.
     *
     * @param array $data Backup record data.
     */
    protected function process_lessonmark(array $data): void {
        global $DB;

        $record = (object) $data;
        $record->course = $this->get_courseid();
        $newitemid = $DB->insert_record('lessonmark', $record);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Restores intro and teaching-image files into the new module context.
     */
    protected function after_execute(): void {
        $this->add_related_files('mod_lessonmark', 'intro', null);
        $this->add_related_files('mod_lessonmark', 'content', null);
    }
}
