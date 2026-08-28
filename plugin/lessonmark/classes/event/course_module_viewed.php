<?php
/**
 * LessonMark course-module viewed event.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\event;

/** Records views of a LessonMark resource. */
final class course_module_viewed extends \core\event\course_module_viewed {
    /** Initialises event metadata. */
    protected function init(): void {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'lessonmark';
    }

    /** @return string Event name. */
    public static function get_name(): string {
        return get_string('eventcoursemoduleviewed', 'mod_lessonmark');
    }

    /** @return string Event description. */
    public function get_description(): string {
        return "The user with id '{$this->userid}' viewed the LessonMark resource with id '{$this->objectid}'.";
    }

    /** @return \moodle_url Viewed URL. */
    public function get_url(): \moodle_url {
        return new \moodle_url('/mod/lessonmark/view.php', ['id' => $this->contextinstanceid]);
    }

    /** @return array Restore mapping. */
    public static function get_objectid_mapping(): array {
        return ['db' => 'lessonmark', 'restore' => 'lessonmark'];
    }
}

