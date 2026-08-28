<?php
/**
 * Tests for LessonMark source normalisation.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/** @covers \mod_lessonmark\local\source_normalizer */
final class source_normalizer_test extends \advanced_testcase {
    /** Raw HTML outside code is escaped. */
    public function test_neutralises_raw_html_outside_code(): void {
        $normalizer = new source_normalizer();
        $actual = $normalizer->neutralise_raw_html("Before <script>alert('x')</script> after");
        $this->assertSame("Before &lt;script&gt;alert('x')&lt;/script&gt; after", $actual);
    }

    /** Inline code remains Markdown code syntax. */
    public function test_preserves_inline_code(): void {
        $normalizer = new source_normalizer();
        $actual = $normalizer->neutralise_raw_html('Use `<section>` but not <section>.');
        $this->assertSame('Use `<section>` but not &lt;section&gt;.', $actual);
    }

    /** Fenced code remains untouched for the Markdown parser. */
    public function test_preserves_fenced_code(): void {
        $normalizer = new source_normalizer();
        $source = "```html\n<script>alert('x')</script>\n```\n<div>text</div>";
        $expected = "```html\n<script>alert('x')</script>\n```\n&lt;div&gt;text&lt;/div&gt;";
        $this->assertSame($expected, $normalizer->neutralise_raw_html($source));
    }
}

