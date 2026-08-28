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
 * Privacy provider tests.
 *
 * @package   mod_lessonmark
 * @category  test
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\privacy;

/**
 * Verifies LessonMark's audited null-provider declaration.
 */
#[\PHPUnit\Framework\Attributes\CoversClass(provider::class)]
final class provider_test extends \advanced_testcase {
    /**
     * LessonMark stores course-owned content but no user-linked records or preferences.
     */
    public function test_declares_a_valid_null_provider_reason(): void {
        $this->assertInstanceOf(
            \core_privacy\local\metadata\null_provider::class,
            new provider()
        );
        $this->assertSame('privacy:metadata', provider::get_reason());
        $this->assertNotSame('', get_string(provider::get_reason(), 'mod_lessonmark'));
    }
}
