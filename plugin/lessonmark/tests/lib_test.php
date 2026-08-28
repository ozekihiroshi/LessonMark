<?php
/**
 * Tests for LessonMark library callbacks.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark;

/** Tests basic instance CRUD. */
final class lib_test extends \advanced_testcase {
    /** Markdown source remains unchanged through CRUD. */
    public function test_instance_crud_preserves_markdown_source(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/lessonmark/lib.php');
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $record = (object) [
            'course' => $course->id,
            'name' => 'Lesson one',
            'intro' => '',
            'introformat' => FORMAT_HTML,
            'markdownsource' => "# Original\n\nContent",
        ];
        $id = lessonmark_add_instance($record);
        $stored = $DB->get_record('lessonmark', ['id' => $id], '*', MUST_EXIST);
        $this->assertSame($record->markdownsource, $stored->markdownsource);
        $stored->instance = $id;
        $stored->markdownsource = "# Updated\n\nStill Markdown";
        $this->assertTrue(lessonmark_update_instance($stored));
        $this->assertSame($stored->markdownsource, $DB->get_field('lessonmark', 'markdownsource', ['id' => $id]));
        $this->assertTrue(lessonmark_delete_instance($id));
        $this->assertFalse($DB->record_exists('lessonmark', ['id' => $id]));
    }
}

