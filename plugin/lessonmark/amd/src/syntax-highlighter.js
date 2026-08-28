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
 * Applies Moodle's bundled Prism highlighter to LessonMark code blocks.
 *
 * @module     mod_lessonmark/syntax-highlighter
 * @copyright  2026 Hiroshi Ozeki
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Prism from './prism-languages';

/**
 * Highlights supported code blocks below one root node.
 *
 * @param {ParentNode} root Content root.
 */
export const highlight = root => {
    if (!root || typeof root.querySelectorAll !== 'function') {
        return;
    }
    Prism.plugins.customClass.prefix('prism-');
    root.querySelectorAll('pre.mod_lessonmark-code code[class*="language-"]').forEach(code => {
        if (code.dataset.lessonmarkHighlighted === 'true') {
            return;
        }
        Prism.highlightElement(code);
        code.dataset.lessonmarkHighlighted = 'true';
    });
};

/**
 * Initialises syntax highlighting on a student-facing document.
 *
 * @param {String} rootSelector Content-root selector.
 */
export const init = (rootSelector = '.mod_lessonmark-content') => {
    highlight(document.querySelector(rootSelector));
};
