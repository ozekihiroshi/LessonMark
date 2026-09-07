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
 * Tests for conditional local browser assets.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/**
 * Tests fixed marker detection without executing author input.
 */
#[\PHPUnit\Framework\Attributes\CoversClass(browser_assets::class)]
final class browser_assets_test extends \advanced_testcase {
    /**
     * Tests conditional mathematics asset detection.
     */
    public function test_detects_only_supported_math_markers(): void {
        $this->assertTrue(browser_assets::requires_math('<code>math:x^2</code>'));
        $this->assertTrue(browser_assets::requires_math('<code>asciimath:a/b</code>'));
        $this->assertTrue(browser_assets::requires_math('<code class="language-latex">x</code>'));
        $this->assertFalse(browser_assets::requires_math('<code>mathematics:x</code>'));
        $this->assertFalse(browser_assets::requires_math('<p>language-math</p>'));
        $this->assertFalse(browser_assets::requires_math('<p>math:x</p>'));
        $this->assertFalse(browser_assets::requires_math('<code class="language-python">x</code>'));
    }

    /**
     * Tests conditional Mermaid asset detection.
     */
    public function test_detects_only_mermaid_language_marker(): void {
        $this->assertTrue(browser_assets::requires_mermaid('<code class="language-mermaid">A--&gt;B</code>'));
        $this->assertFalse(browser_assets::requires_mermaid('<p>language-mermaid</p>'));
        $this->assertFalse(browser_assets::requires_mermaid('<code class="language-python">pass</code>'));
    }
}
