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
}
