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
 * Browser asset detection for safe rendered LessonMark HTML.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/**
 * Selects optional local browser renderers from fixed HTML markers.
 */
final class browser_assets {
    /**
     * Whether formula rendering assets are required.
     *
     * @param string $html Safe enhanced HTML.
     * @return bool
     */
    public static function requires_math(string $html): bool {
        return preg_match(
            '/<code\b[^>]*\bclass="[^"]*\blanguage-(?:math|latex|asciimath)\b[^"]*"[^>]*>'
                . '|<code(?:\s[^>]*)?>(?:math|latex|asciimath):/',
            $html
        ) === 1;
    }

    /**
     * Whether Mermaid rendering assets are required.
     *
     * @param string $html Safe enhanced HTML.
     * @return bool
     */
    public static function requires_mermaid(string $html): bool {
        return preg_match('/\bclass="[^"]*\blanguage-mermaid\b[^"]*"/', $html) === 1;
    }
}
