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
 * Moodle-core Markdown renderer adapter.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/**
 * Renders LessonMark source through Moodle's formatting API.
 */
final class moodle_markdown_renderer implements markdown_renderer_interface {
    /** Maximum source size accepted by v0.1. */
    public const MAX_SOURCE_BYTES = 524288;

    /** @var source_normalizer Raw HTML neutraliser. */
    private source_normalizer $normalizer;

    /** @var teaching_document_enhancer Teaching-document HTML enhancer. */
    private teaching_document_enhancer $enhancer;

    /** @var int|null Draft item ID used only for unsaved Preview. */
    private ?int $draftitemid;

    /**
     * Creates a Moodle Markdown renderer.
     *
     * @param source_normalizer|null $normalizer Optional normaliser.
     * @param teaching_document_enhancer|null $enhancer Optional teaching-document enhancer.
     * @param int|null $draftitemid Optional current-user draft item ID.
     */
    public function __construct(
        ?source_normalizer $normalizer = null,
        ?teaching_document_enhancer $enhancer = null,
        ?int $draftitemid = null
    ) {
        $this->normalizer = $normalizer ?? new source_normalizer();
        $this->enhancer = $enhancer ?? new teaching_document_enhancer();
        $this->draftitemid = $draftitemid;
    }

    /**
     * Renders one Markdown source document.
     *
     * @param string $source Markdown source.
     * @param \context $context Moodle context.
     * @return rendered_document Rendered document.
     */
    public function render(string $source, \context $context): rendered_document {
        if (strlen($source) > self::MAX_SOURCE_BYTES || preg_match('//u', $source) !== 1) {
            throw new \invalid_parameter_exception('Invalid LessonMark source.');
        }
        $normalised = $this->normalizer->neutralise_raw_html($source);
        $html = format_text($normalised, FORMAT_MARKDOWN, [
            'context' => $context,
            'trusted' => false,
            'clean' => true,
            'filter' => false,
            'para' => false,
            'allowid' => false,
        ]);
        $safehtml = clean_text($html, FORMAT_HTML, ['allowid' => false]);
        $document = $this->enhancer->enhance($safehtml);
        $content = content_files::rewrite_urls(
            $document->get_content_html(),
            $context,
            $this->draftitemid
        );
        return new rendered_document($content, $document->get_toc(), $document->get_diagnostics());
    }
}
