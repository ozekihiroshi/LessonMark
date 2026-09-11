<?php
// This file is part of Moodle - https://moodle.org/
// Licensed under the GNU GPL v3 or later: https://www.gnu.org/copyleft/gpl.html

/**
 * Playlist access and ordering tests.
 * @package mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_lessonmark\local;

/**
 * Tests playlist construction without exposing hidden teaching materials.
 */
#[\PHPUnit\Framework\Attributes\CoversClass(course_presentation::class)]
final class course_presentation_test extends \advanced_testcase {
    /**
     * Playlist follows sections, not creation order, and excludes nonlisted modules.
     */
    public function test_order_and_visibility(): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['numsections' => 3]);
        $later = $generator->create_module('lessonmark', ['course' => $course->id, 'section' => 2]);
        $first = $generator->create_module('lessonmark', ['course' => $course->id, 'section' => 1]);
        $second = $generator->create_module('lessonmark', ['course' => $course->id, 'section' => 1]);
        $generator->create_module('page', ['course' => $course->id, 'section' => 1]);
        $generator->create_module('lessonmark', ['course' => $course->id, 'visible' => 0]);
        $generator->create_module('lessonmark', ['course' => $course->id, 'visibleoncoursepage' => 0]);
        $generator->create_module('lessonmark', ['course' => $course->id, 'section' => 3]);
        set_section_visible($course->id, 3, 0);
        $ids = array_map(static fn($cm): int => (int) $cm->id, course_presentation::modules($course));
        $this->assertSame([(int) $first->cmid, (int) $second->cmid, (int) $later->cmid], $ids);
    }

    /**
     * Student-specific availability and capability restrictions remove entries.
     */
    public function test_student_access(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $student = $generator->create_user();
        $generator->enrol_user($student->id, $course->id, 'student');
        $allowed = $generator->create_module('lessonmark', ['course' => $course->id]);
        $denied = $generator->create_module('lessonmark', ['course' => $course->id]);
        $blocked = $generator->create_module('lessonmark', ['course' => $course->id]);
        $DB->set_field('course_modules', 'availability', json_encode([
            'op' => '&', 'c' => [['type' => 'date', 'd' => '>=', 't' => time() + YEARSECS]], 'showc' => [false],
        ]), ['id' => $blocked->cmid]);
        set_config('enableavailability', 1);
        rebuild_course_cache($course->id, true);
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        assign_capability('mod/lessonmark:view', CAP_PROHIBIT, $roleid, \context_module::instance($denied->cmid)->id);
        $this->setUser($student);
        $ids = array_map(static fn($cm): int => (int) $cm->id, course_presentation::modules($course));
        $this->assertSame([(int) $allowed->cmid], $ids);
    }

    /**
     * Listing never crosses course boundaries or marks unvisited modules as viewed.
     */
    public function test_scope_and_no_completion(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        set_config('enablecompletion', 1);
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['enablecompletion' => 1]);
        $other = $generator->create_course();
        $generator->create_module('lessonmark', ['course' => $other->id]);
        $this->assertSame([], course_presentation::modules($course));
        $lesson = $generator->create_module('lessonmark', [
            'course' => $course->id, 'completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1,
        ]);
        $student = $generator->create_user();
        $generator->enrol_user($student->id, $course->id, 'student');
        $this->setUser($student);
        $events = $this->redirectEvents();
        $this->assertCount(1, course_presentation::modules($course));
        $this->assertFalse($DB->record_exists('course_modules_completion', [
            'coursemoduleid' => $lesson->cmid, 'userid' => $student->id,
        ]));
        $this->assertCount(0, $events->get_events());
        $events->close();
    }
}
