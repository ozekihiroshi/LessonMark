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
 * Tests for LessonMark source normalisation.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/**
 * Tests raw HTML neutralisation around Markdown code syntax.
 */
#[\PHPUnit\Framework\Attributes\CoversClass(source_normalizer::class)]
final class source_normalizer_test extends \advanced_testcase {
    /**
     * Tests that raw HTML outside code is escaped.
     */
    public function test_neutralises_raw_html_outside_code(): void {
        $normalizer = new source_normalizer();
        $actual = $normalizer->neutralise_raw_html("Before <script>alert('x')</script> after");
        $this->assertSame("Before &lt;script&gt;alert('x')&lt;/script&gt; after", $actual);
    }

    /**
     * Tests that inline code remains Markdown code syntax.
     */
    public function test_preserves_inline_code(): void {
        $normalizer = new source_normalizer();
        $backtick = chr(96);
        $source = 'Use ' . $backtick . '<section>' . $backtick . ' but not <section>.';
        $expected = 'Use ' . $backtick . '<section>' . $backtick . ' but not &lt;section&gt;.';
        $this->assertSame($expected, $normalizer->neutralise_raw_html($source));
    }

    /**
     * Tests that fenced code remains untouched for the Markdown parser.
     */
    public function test_preserves_fenced_code(): void {
        $normalizer = new source_normalizer();
        $fence = str_repeat(chr(96), 3);
        $source = $fence . "html\n<script>alert('x')</script>\n" . $fence . "\n<div>text</div>";
        $expected = $fence . "html\n<script>alert('x')</script>\n" . $fence . "\n&lt;div&gt;text&lt;/div&gt;";
        $this->assertSame($expected, $normalizer->neutralise_raw_html($source));
    }
}
