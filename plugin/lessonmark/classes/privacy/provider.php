<?php
/**
 * Privacy provider for LessonMark.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\privacy;

/** Declares that LessonMark stores no personal data of its own. */
final class provider implements \core_privacy\local\metadata\null_provider {
    /** @return string Null-provider reason. */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}

