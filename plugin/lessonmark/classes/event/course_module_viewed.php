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
 * LessonMark course-module viewed event.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\event;

/**
 * Records views of a LessonMark resource.
 */
final class course_module_viewed extends \core\event\course_module_viewed {
    /**
     * Initialises event metadata.
     */
    protected function init(): void {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'lessonmark';
    }

    /**
     * Returns the localised event name.
     *
     * @return string Event name.
     */
    public static function get_name(): string {
        return get_string('eventcoursemoduleviewed', 'mod_lessonmark');
    }

    /**
     * Returns a human-readable event description.
     *
     * @return string Event description.
     */
    public function get_description(): string {
        return "The user with id '{$this->userid}' viewed the LessonMark resource with id '{$this->objectid}'.";
    }

    /**
     * Returns the viewed resource URL.
     *
     * @return \moodle_url Viewed URL.
     */
    public function get_url(): \moodle_url {
        return new \moodle_url('/mod/lessonmark/view.php', ['id' => $this->contextinstanceid]);
    }

    /**
     * Returns the restore mapping for the event object id.
     *
     * @return array Restore mapping.
     */
    public static function get_objectid_mapping(): array {
        return ['db' => 'lessonmark', 'restore' => 'lessonmark'];
    }
}
