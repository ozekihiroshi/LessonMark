<?php
// This file is part of Moodle - https://moodle.org/
// Licensed under the GNU GPL v3 or later: https://www.gnu.org/copyleft/gpl.html

/**
 * Course presentation playlist.
 * @package mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_lessonmark\local;

/**
 * Builds a playlist without loading content or recording activity views.
 */
final class course_presentation {
    /**
     * Accessible, course-listed LessonMark modules in section/module order.
     * Hidden and stealth activities are deliberately excluded, including for teachers.
     *
     * @param \stdClass $course Course record.
     * @return \cm_info[] Ordered modules accessible to the current user.
     */
    public static function modules(\stdClass $course): array {
        $modinfo = get_fast_modinfo($course);
        $result = [];
        foreach ($modinfo->get_sections() as $ids) {
            foreach ($ids as $id) {
                $cm = $modinfo->get_cm($id);
                $section = $modinfo->get_section_info($cm->sectionnum);
                if ($cm->modname !== 'lessonmark' || !$cm->visible || !$cm->visibleoncoursepage ||
                        !$section->visible || !$cm->uservisible || $cm->deletioninprogress) {
                    continue;
                }
                if (has_capability('mod/lessonmark:view', \context_module::instance($cm->id))) {
                    $result[] = $cm;
                }
            }
        }
        return $result;
    }
}
