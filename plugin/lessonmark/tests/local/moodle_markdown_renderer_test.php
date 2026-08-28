<?php
/**
 * Tests for the Moodle-core Markdown renderer adapter.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/** @covers \mod_lessonmark\local\moodle_markdown_renderer */
final class moodle_markdown_renderer_test extends \advanced_testcase {
    /** Markdown is converted to HTML. */
    public function test_renders_markdown(): void {
        $renderer = new moodle_markdown_renderer();
        $document = $renderer->render("# Heading\n\n**Strong**", \context_system::instance());
        $this->assertStringContainsString('<h1>Heading</h1>', $document->get_content_html());
        $this->assertStringContainsString('<strong>Strong</strong>', $document->get_content_html());
        $this->assertSame([], $document->get_toc());
        $this->assertSame([], $document->get_diagnostics());
    }

    /** Raw HTML is visible as text and not executable markup. */
    public function test_neutralises_raw_html(): void {
        $renderer = new moodle_markdown_renderer();
        $html = $renderer->render('<script>alert(1)</script>', \context_system::instance())->get_content_html();
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    /** Oversized source is rejected. */
    public function test_rejects_oversized_source(): void {
        $renderer = new moodle_markdown_renderer();
        $this->expectException(\invalid_parameter_exception::class);
        $renderer->render(str_repeat('a', moodle_markdown_renderer::MAX_SOURCE_BYTES + 1), \context_system::instance());
    }
}

