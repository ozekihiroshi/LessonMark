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
 * Tests for the Moodle-core Markdown renderer adapter.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/**
 * Tests the Moodle-core Markdown renderer adapter.
 */
#[\PHPUnit\Framework\Attributes\CoversClass(moodle_markdown_renderer::class)]
final class moodle_markdown_renderer_test extends \advanced_testcase {
    /**
     * Tests conversion of basic Markdown to HTML.
     */
    public function test_renders_markdown(): void {
        $renderer = new moodle_markdown_renderer();
        $document = $renderer->render("# Heading\n\n**Strong**", \context_system::instance());
        $this->assertStringContainsString('<h1>Heading</h1>', $document->get_content_html());
        $this->assertStringContainsString('<strong>Strong</strong>', $document->get_content_html());
        $this->assertSame([], $document->get_toc());
        $this->assertSame([], $document->get_diagnostics());
    }

    /**
     * Tests that raw HTML remains visible but cannot execute.
     */
    public function test_neutralises_raw_html(): void {
        $renderer = new moodle_markdown_renderer();
        $html = $renderer->render('<script>alert(1)</script>', \context_system::instance())->get_content_html();
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    /**
     * Tests rejection of sources over the configured limit.
     */
    public function test_rejects_oversized_source(): void {
        $renderer = new moodle_markdown_renderer();
        $this->expectException(\invalid_parameter_exception::class);
        $renderer->render(str_repeat('a', moodle_markdown_renderer::MAX_SOURCE_BYTES + 1), \context_system::instance());
    }
}
