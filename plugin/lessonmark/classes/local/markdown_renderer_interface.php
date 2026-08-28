<?php
/**
 * Renderer contract for LessonMark.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/** Converts Markdown source into a safe rendered document. */
interface markdown_renderer_interface {
    /**
     * @param string $source Markdown source.
     * @param \context $context Moodle context.
     * @return rendered_document
     */
    public function render(string $source, \context $context): rendered_document;
}

