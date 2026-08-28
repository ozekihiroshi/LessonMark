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
 * Tests for LessonMark library callbacks.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark;

/**
 * Tests basic instance CRUD callbacks.
 */
#[\PHPUnit\Framework\Attributes\CoversFunction('lessonmark_add_instance')]
#[\PHPUnit\Framework\Attributes\CoversFunction('lessonmark_update_instance')]
#[\PHPUnit\Framework\Attributes\CoversFunction('lessonmark_delete_instance')]
final class lib_test extends \advanced_testcase {
    /**
     * Tests that Markdown source remains unchanged through CRUD.
     */
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
