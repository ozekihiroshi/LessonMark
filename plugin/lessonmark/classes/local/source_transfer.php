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
 * Import and export rules for Markdown source files.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/**
 * Keeps source-file validation independent from browser and response code.
 */
final class source_transfer {
    /** UTF-8 byte order mark. */
    private const UTF8_BOM = "\xEF\xBB\xBF";

    /**
     * Validates and normalises one imported Markdown file.
     *
     * Browser textareas use LF line endings, so imported CRLF and CR are
     * normalised before the source becomes an editable Moodle draft.
     *
     * @param string $content Uploaded file content.
     * @return string Editable Markdown source.
     */
    public static function normalise_import(string $content): string {
        if (str_starts_with($content, self::UTF8_BOM)) {
            $content = substr($content, strlen(self::UTF8_BOM));
        }
        if (preg_match('//u', $content) !== 1) {
            throw new \invalid_parameter_exception(get_string('errorinvalidutf8', 'mod_lessonmark'));
        }
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        if (strlen($content) > moodle_markdown_renderer::MAX_SOURCE_BYTES) {
            throw new \invalid_parameter_exception(get_string('errorsourcetoolarge', 'mod_lessonmark'));
        }
        return $content;
    }

    /**
     * Creates a safe download filename from the activity name.
     *
     * @param string $name LessonMark activity name.
     * @return string Safe filename ending in .md.
     */
    public static function export_filename(string $name): string {
        $filename = clean_filename($name);
        $basename = preg_replace('/\.md$/iu', '', $filename) ?? '';
        $basename = trim($basename, " .\t\n\r\0\x0B");
        if ($basename === '') {
            $basename = 'lessonmark';
        }
        return $basename . '.md';
    }
}
