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
 * Renderer security boundary tests.
 *
 * @package   mod_lessonmark
 * @category  test
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/**
 * Verifies that untrusted Markdown cannot create active browser content.
 */
#[\PHPUnit\Framework\Attributes\CoversClass(moodle_markdown_renderer::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(source_normalizer::class)]
final class renderer_security_test extends \advanced_testcase {
    /**
     * Raw active HTML is visible as source text instead of becoming DOM elements.
     */
    public function test_neutralises_active_raw_html(): void {
        $source = '<img src=x onerror="alert(1)">'
            . "\n\n<iframe src=\"https://example.com\"></iframe>"
            . "\n\n<style>body { display: none; }</style>";

        $html = (new moodle_markdown_renderer())->render(
            $source,
            \context_system::instance()
        )->get_content_html();

        $this->assertStringNotContainsString('<img src=', $html);
        $this->assertStringNotContainsString('<iframe', $html);
        $this->assertStringNotContainsString('<style>', $html);
        $this->assertStringContainsString('&lt;img', $html);
        $this->assertStringContainsString('&lt;iframe', $html);
    }

    /**
     * Dangerous Markdown URL schemes never survive as executable href attributes.
     */
    public function test_removes_dangerous_link_schemes(): void {
        $source = '[JavaScript](javascript:alert(1))'
            . "\n\n[Data](data:text/html,%3Cscript%3Ealert(1)%3C/script%3E)";

        $html = (new moodle_markdown_renderer())->render(
            $source,
            \context_system::instance()
        )->get_content_html();

        $this->assertStringNotContainsString('href="javascript:', $html);
        $this->assertStringNotContainsString('href="data:', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    /**
     * Invalid UTF-8 is rejected before Moodle formatting or DOM processing.
     */
    public function test_rejects_invalid_utf8(): void {
        $this->expectException(\invalid_parameter_exception::class);
        (new moodle_markdown_renderer())->render("Broken \xC3\x28", \context_system::instance());
    }
}
