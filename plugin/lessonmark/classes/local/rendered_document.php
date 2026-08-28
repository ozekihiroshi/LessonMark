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
 * Rendered LessonMark document.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/**
 * Holds safe HTML and renderer metadata.
 */
final class rendered_document {
    /** @var string Safe rendered HTML. */
    private string $contenthtml;

    /** @var array Table-of-contents data. */
    private array $toc;

    /** @var array Renderer diagnostics. */
    private array $diagnostics;

    /**
     * Creates an immutable rendering result.
     *
     * @param string $contenthtml Safe rendered HTML.
     * @param array $toc Table-of-contents data.
     * @param array $diagnostics Renderer diagnostics.
     */
    public function __construct(string $contenthtml, array $toc = [], array $diagnostics = []) {
        $this->contenthtml = $contenthtml;
        $this->toc = $toc;
        $this->diagnostics = $diagnostics;
    }

    /**
     * Returns safe rendered HTML.
     *
     * @return string Safe HTML.
     */
    public function get_content_html(): string {
        return $this->contenthtml;
    }

    /**
     * Returns table-of-contents data.
     *
     * @return array Table-of-contents data.
     */
    public function get_toc(): array {
        return $this->toc;
    }

    /**
     * Returns renderer diagnostics.
     *
     * @return array Renderer diagnostics.
     */
    public function get_diagnostics(): array {
        return $this->diagnostics;
    }
}
