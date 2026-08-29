<?php
// This file is part of Moodle - http://moodle.org/
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
 * LessonMark-specific Behat steps.
 *
 * @package    mod_lessonmark
 * @copyright  2026 Hiroshi Ozeki
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Behat\Gherkin\Node\PyStringNode;

/**
 * LessonMark-specific Behat steps.
 */
class behat_mod_lessonmark extends behat_base {
    /**
     * Sets the Markdown editor to an actual multiline value.
     *
     * @When /^I set the LessonMark Markdown source to:$/
     * @param PyStringNode $source Markdown source supplied by the feature.
     */
    public function set_lessonmark_markdown_source(PyStringNode $source): void {
        $field = $this->getSession()->getPage()->findField('Markdown source');
        if ($field === null) {
            throw new \RuntimeException('The LessonMark Markdown source field was not found.');
        }

        $field->setValue($source->getRaw());
    }

    /**
     * Confirms that a tall preview does not push the source textarea down.
     *
     * @Then /^the LessonMark source editor should stay aligned with its preview$/
     */
    public function source_editor_should_stay_aligned_with_preview(): void {
        $gap = $this->getSession()->evaluateScript(<<<'JS'
            (function() {
                var source = document.querySelector('#id_markdownsource');
                var sourceField = source ? source.closest('.mod_lessonmark-editor__source') : null;
                var preview = document.querySelector('.mod_lessonmark-editor');

                if (!source || !sourceField || !preview) {
                    return null;
                }

                var originalMinHeight = preview.style.minHeight;
                preview.style.minHeight = '80rem';
                var gap = source.getBoundingClientRect().top - sourceField.getBoundingClientRect().top;
                preview.style.minHeight = originalMinHeight;

                return gap;
            }())
            JS);

        if (!is_numeric($gap)) {
            throw new \RuntimeException('The LessonMark editor panes were not found.');
        }

        $maximumgap = 120.0;
        if ((float) $gap > $maximumgap) {
            throw new \RuntimeException(sprintf(
                'The Markdown source started %.1f pixels below its pane; expected at most %.1f pixels.',
                (float) $gap,
                $maximumgap,
            ));
        }
    }

    /**
     * Confirms that the source remains available while the document scrolls.
     *
     * @Then /^the LessonMark source editor should remain visible while the preview scrolls$/
     */
    public function source_editor_should_remain_visible_while_preview_scrolls(): void {
        $top = $this->getSession()->evaluateScript(<<<'JS'
            (function() {
                var source = document.querySelector('#id_markdownsource');
                var sourceField = source ? source.closest('.mod_lessonmark-editor__source') : null;
                var preview = document.querySelector('.mod_lessonmark-editor');
                var panes = document.querySelector('.mod_lessonmark-editor__panes');

                if (!sourceField || !preview || !panes) {
                    return null;
                }

                var originalMinHeight = preview.style.minHeight;
                var originalScrollBehavior = document.documentElement.style.scrollBehavior;
                var originalScrollY = window.scrollY;
                preview.style.minHeight = '160rem';
                document.documentElement.style.scrollBehavior = 'auto';

                var panesTop = panes.getBoundingClientRect().top + window.scrollY;
                window.scrollTo(0, panesTop + 320);
                var sourceTop = sourceField.getBoundingClientRect().top;

                window.scrollTo(0, originalScrollY);
                document.documentElement.style.scrollBehavior = originalScrollBehavior;
                preview.style.minHeight = originalMinHeight;

                return sourceTop;
            }())
            JS);

        if (!is_numeric($top)) {
            throw new \RuntimeException('The LessonMark editor panes were not found.');
        }

        $minimumtop = 0.0;
        $maximumtop = 80.0;
        if ((float) $top < $minimumtop || (float) $top > $maximumtop) {
            throw new \RuntimeException(sprintf(
                'The Markdown source was at %.1f pixels after scrolling; expected between %.1f and %.1f pixels.',
                (float) $top,
                $minimumtop,
                $maximumtop,
            ));
        }
    }
}
