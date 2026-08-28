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
 * Backup, restore, and course duplicate integration tests.
 *
 * @package   mod_lessonmark
 * @category  test
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/phpunit/classes/restore_date_testcase.php');

/**
 * Verifies that LessonMark's source of truth and File API assets survive Moodle lifecycle operations.
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\backup_lessonmark_activity_task::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\backup_lessonmark_activity_structure_step::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\restore_lessonmark_activity_task::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\restore_lessonmark_activity_structure_step::class)]
final class backup_restore_test extends \restore_date_testcase {
    /**
     * A full course restore preserves content and remaps LessonMark links.
     */
    public function test_course_backup_restore_preserves_source_files_and_links(): void {
        global $CFG, $DB;

        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['startdate' => $this->startdate]);
        $target = $generator->create_module('lessonmark', [
            'course' => $course->id,
            'name' => 'Linked lesson',
            'markdownsource' => '# Linked lesson',
        ]);
        $source = $generator->create_module('lessonmark', [
            'course' => $course->id,
            'name' => 'Source lesson',
            'intro' => '<p>Teacher introduction</p>',
            'introformat' => FORMAT_HTML,
            'markdownsource' => '# Source lesson',
            'displayoptions' => '{"toc":true}',
        ]);
        $source->markdownsource = implode("\n\n", [
            '# Source lesson',
            '![Diagram](@@PLUGINFILE@@/images/diagram.svg)',
            '[Next lesson](' . $CFG->wwwroot . '/mod/lessonmark/view.php?id=' . $target->cmid . ')',
            '[Course lessons](' . $CFG->wwwroot . '/mod/lessonmark/index.php?id=' . $course->id . ')',
        ]);
        $DB->update_record('lessonmark', $source);
        $this->create_module_file($source->cmid, 'content', '/images/', 'diagram.svg', '<svg></svg>');
        $this->create_module_file($source->cmid, 'intro', '/', 'intro.txt', 'intro attachment');

        $newcourseid = $this->backup_and_restore($course);

        $restoredsource = $DB->get_record('lessonmark', [
            'course' => $newcourseid,
            'name' => 'Source lesson',
        ], '*', MUST_EXIST);
        $restoredtarget = $DB->get_record('lessonmark', [
            'course' => $newcourseid,
            'name' => 'Linked lesson',
        ], '*', MUST_EXIST);
        $restoredsourcecm = get_coursemodule_from_instance(
            'lessonmark',
            $restoredsource->id,
            $newcourseid,
            false,
            MUST_EXIST
        );
        $restoredtargetcm = get_coursemodule_from_instance(
            'lessonmark',
            $restoredtarget->id,
            $newcourseid,
            false,
            MUST_EXIST
        );

        $this->assertSame($source->intro, $restoredsource->intro);
        $this->assertSame($source->introformat, $restoredsource->introformat);
        $this->assertSame($source->displayoptions, $restoredsource->displayoptions);
        $this->assertStringContainsString(
            '![Diagram](@@PLUGINFILE@@/images/diagram.svg)',
            $restoredsource->markdownsource
        );
        $this->assertStringContainsString(
            $CFG->wwwroot . '/mod/lessonmark/view.php?id=' . $restoredtargetcm->id,
            $restoredsource->markdownsource
        );
        $this->assertStringContainsString(
            $CFG->wwwroot . '/mod/lessonmark/index.php?id=' . $newcourseid,
            $restoredsource->markdownsource
        );
        $this->assertStringNotContainsString(
            $CFG->wwwroot . '/mod/lessonmark/view.php?id=' . $target->cmid,
            $restoredsource->markdownsource
        );
        $this->assert_module_file_exists($restoredsourcecm->id, 'content', '/images/', 'diagram.svg');
        $this->assert_module_file_exists($restoredsourcecm->id, 'intro', '/', 'intro.txt');
    }

    /**
     * Course duplicate preserves files and remaps a self-link to the new module.
     */
    public function test_course_duplicate_preserves_source_files_and_remaps_self_link(): void {
        global $CFG, $DB;

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $source = $generator->create_module('lessonmark', [
            'course' => $course->id,
            'name' => 'Duplicated lesson',
            'intro' => '<p>Keep this introduction</p>',
            'introformat' => FORMAT_HTML,
            'markdownsource' => '# Duplicated lesson',
            'displayoptions' => '{"toc":false}',
        ]);
        $source->markdownsource = implode("\n\n", [
            '# Duplicated lesson',
            '![Diagram](@@PLUGINFILE@@/diagram.svg)',
            '[This lesson](' . $CFG->wwwroot . '/mod/lessonmark/view.php?id=' . $source->cmid . ')',
        ]);
        $DB->update_record('lessonmark', $source);
        $this->create_module_file($source->cmid, 'content', '/', 'diagram.svg', '<svg></svg>');

        $newcm = \core_courseformat\formatactions::cm($course)->duplicate($source->cmid);
        $duplicate = $DB->get_record('lessonmark', ['id' => $newcm->instance], '*', MUST_EXIST);

        $this->assertNotSame($source->id, $duplicate->id);
        $this->assertSame($source->intro, $duplicate->intro);
        $this->assertSame($source->introformat, $duplicate->introformat);
        $this->assertSame($source->displayoptions, $duplicate->displayoptions);
        $this->assertStringContainsString('![Diagram](@@PLUGINFILE@@/diagram.svg)', $duplicate->markdownsource);
        $this->assertStringContainsString(
            $CFG->wwwroot . '/mod/lessonmark/view.php?id=' . $newcm->id,
            $duplicate->markdownsource
        );
        $this->assertStringNotContainsString(
            $CFG->wwwroot . '/mod/lessonmark/view.php?id=' . $source->cmid,
            $duplicate->markdownsource
        );
        $this->assert_module_file_exists($newcm->id, 'content', '/', 'diagram.svg');
    }

    /**
     * Creates a permanent module file for a lifecycle test.
     *
     * @param int $cmid Course-module id.
     * @param string $filearea File API area.
     * @param string $filepath File path including leading and trailing slash.
     * @param string $filename File name.
     * @param string $content File content.
     */
    private function create_module_file(
        int $cmid,
        string $filearea,
        string $filepath,
        string $filename,
        string $content
    ): void {
        $context = \context_module::instance($cmid);
        get_file_storage()->create_file_from_string([
            'contextid' => $context->id,
            'component' => 'mod_lessonmark',
            'filearea' => $filearea,
            'itemid' => 0,
            'filepath' => $filepath,
            'filename' => $filename,
        ], $content);
    }

    /**
     * Asserts that a restored file exists in its new module context.
     *
     * @param int $cmid Course-module id.
     * @param string $filearea File API area.
     * @param string $filepath File path including leading and trailing slash.
     * @param string $filename File name.
     */
    private function assert_module_file_exists(
        int $cmid,
        string $filearea,
        string $filepath,
        string $filename
    ): void {
        $context = \context_module::instance($cmid);
        $file = get_file_storage()->get_file(
            $context->id,
            'mod_lessonmark',
            $filearea,
            0,
            $filepath,
            $filename
        );
        $this->assertNotFalse($file);
    }
}
