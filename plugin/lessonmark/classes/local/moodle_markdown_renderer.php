<?php
/**
 * Moodle-core Markdown renderer adapter.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/** Renders LessonMark source through Moodle's formatting API. */
final class moodle_markdown_renderer implements markdown_renderer_interface {
    /** Maximum source size accepted by v0.1. */
    public const MAX_SOURCE_BYTES = 524288;

    /** @var source_normalizer Raw HTML neutraliser. */
    private source_normalizer $normalizer;

    /** @param source_normalizer|null $normalizer Optional normaliser. */
    public function __construct(?source_normalizer $normalizer = null) {
        $this->normalizer = $normalizer ?? new source_normalizer();
    }

    /**
     * @param string $source Markdown source.
     * @param \context $context Moodle context.
     * @return rendered_document
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
        return new rendered_document(clean_text($html, FORMAT_HTML, ['allowid' => false]));
    }
}

