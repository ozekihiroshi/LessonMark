<?php
/**
 * Rendered LessonMark document.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/** Holds safe HTML and renderer metadata. */
final class rendered_document {
    /**
     * @param string $contenthtml Safe rendered HTML.
     * @param array $toc Table-of-contents data.
     * @param array $diagnostics Renderer diagnostics.
     */
    public function __construct(
        private string $contenthtml,
        private array $toc = [],
        private array $diagnostics = [],
    ) {
    }

    /** @return string Safe HTML. */
    public function get_content_html(): string {
        return $this->contenthtml;
    }

    /** @return array Table-of-contents data. */
    public function get_toc(): array {
        return $this->toc;
    }

    /** @return array Renderer diagnostics. */
    public function get_diagnostics(): array {
        return $this->diagnostics;
    }
}

