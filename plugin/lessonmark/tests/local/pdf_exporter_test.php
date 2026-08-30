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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Tests for LessonMark PDF export preparation.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/**
 * Tests the printable transformation and binary generation.
 */
#[\PHPUnit\Framework\Attributes\CoversClass(pdf_exporter::class)]
final class pdf_exporter_test extends \advanced_testcase {
    /**
     * Saved content produces a PDF with expanded answers and embedded images.
     */
    public function test_generate_pdf_from_saved_activity(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['fullname' => 'PDF course']);
        $lessonmark = $this->getDataGenerator()->create_module('lessonmark', [
            'course' => $course->id,
            'name' => 'PDF lesson',
            'markdownsource' => "# PDF lesson\n\n"
                . "![Pixel](@@PLUGINFILE@@/pixel.png)\n\n"
                . "> [!RESPONSE]\n> Explain.\n\n"
                . "> [!ANSWER]\n> **Answer:** Evidence.",
        ]);
        $cm = get_coursemodule_from_instance('lessonmark', $lessonmark->id, $course->id, false, MUST_EXIST);
        $context = \context_module::instance($cm->id);
        get_file_storage()->create_file_from_string([
            'contextid' => $context->id,
            'component' => 'mod_lessonmark',
            'filearea' => content_files::FILEAREA,
            'itemid' => content_files::ITEMID,
            'filepath' => '/',
            'filename' => 'pixel.png',
            'mimetype' => 'image/png',
        ], base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='));

        $bytes = (new pdf_exporter())->generate($lessonmark, $course, $context);
        $this->assertStringStartsWith('%PDF-', $bytes);
        $this->assertStringContainsString('/Subtype /Image', $bytes);
        $this->assertGreaterThan(1000, strlen($bytes));
    }

    /**
     * Export filenames are safe and always end in .pdf.
     */
    public function test_export_filename(): void {
        $this->assertSame('Lesson.pdf', pdf_exporter::export_filename('Lesson.pdf'));
        $this->assertSame('lessonmark.pdf', pdf_exporter::export_filename('...'));
    }
}
