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
 * Structural slide boundaries in Markdown.
 *
 * @package mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_lessonmark\local;

/**
 * Splits source without interpreting author HTML.
 */
final class presentation_source {
    /**
     * Splits standalone markers outside fenced code.
     *
     * @param string $source Saved Markdown.
     * @return string[] Nonempty slides, or one empty slide.
     */
    public static function split(string $source): array {
        $slides = [];
        $lines = [];
        $fence = null;
        $length = 0;
        foreach (preg_split('/\\R/u', $source) ?: [] as $line) {
            if ($fence !== null) {
                $lines[] = $line;
                if (preg_match('/^\\s*' . preg_quote($fence, '/') . '{' . $length . ',}\\s*$/', $line)) {
                    $fence = null;
                }
                continue;
            }
            if (preg_match('/^\\s*(\x60{3,}|~{3,})/', $line, $match)) {
                $fence = $match[1][0];
                $length = strlen($match[1]);
            } else if (preg_match('/^<!-- slide -->[ \\t]*$/', $line)) {
                if (trim(implode("\n", $lines)) !== '') {
                    $slides[] = implode("\n", $lines);
                }
                $lines = [];
                continue;
            }
            $lines[] = $line;
        }
        if (trim(implode("\n", $lines)) !== '') {
            $slides[] = implode("\n", $lines);
        }
        return $slides ?: [''];
    }
}
