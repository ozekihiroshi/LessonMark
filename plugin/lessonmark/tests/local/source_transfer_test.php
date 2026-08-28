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
 * Tests for LessonMark Markdown source transfers.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/**
 * Tests import normalisation and export filenames.
 */
#[\PHPUnit\Framework\Attributes\CoversClass(source_transfer::class)]
final class source_transfer_test extends \advanced_testcase {
    /**
     * Tests BOM removal and browser-compatible line endings.
     */
    public function test_normalises_utf8_bom_and_line_endings(): void {
        $source = "\xEF\xBB\xBF# Title\r\n\rParagraph\nEnd";
        $this->assertSame("# Title\n\nParagraph\nEnd", source_transfer::normalise_import($source));
    }

    /**
     * Tests rejection of invalid UTF-8.
     */
    public function test_rejects_invalid_utf8(): void {
        $this->expectException(\invalid_parameter_exception::class);
        source_transfer::normalise_import("Broken \xC3\x28");
    }

    /**
     * Tests rejection of an oversized source after normalisation.
     */
    public function test_rejects_oversized_source(): void {
        $this->expectException(\invalid_parameter_exception::class);
        source_transfer::normalise_import(str_repeat(
            'x',
            moodle_markdown_renderer::MAX_SOURCE_BYTES + 1
        ));
    }

    /**
     * Tests safe, stable Markdown filenames.
     */
    public function test_builds_safe_export_filename(): void {
        $this->assertSame('Lesson.md', source_transfer::export_filename('Lesson.md'));
        $filename = source_transfer::export_filename('../../Unsafe/name');
        $this->assertStringEndsWith('.md', $filename);
        $this->assertStringNotContainsString('/', $filename);
        $this->assertStringNotContainsString('\\', $filename);
        $this->assertSame('lessonmark.md', source_transfer::export_filename('...'));
    }
}
