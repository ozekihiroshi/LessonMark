// This file is part of Moodle - https://moodle.org/
// Licensed under the GNU GPL v3 or later: https://www.gnu.org/copyleft/gpl.html
/**
 * Temporary print preparation; never stores learner responses or disclosure state.
 * @copyright 2026 Hiroshi Ozeki
 */
(function() {
    'use strict';
    let answers = null;
    let groups = [];
    const prepare = () => {
        if (answers !== null) {
            return;
        }
        answers = Array.from(document.querySelectorAll('.mod_lessonmark-selfcheck__answer'))
            .map(element => ({element, open: element.open}));
        answers.forEach(({element}) => { element.open = true; });
        document.querySelectorAll('.mod_lessonmark-content p').forEach(paragraph => {
            if (paragraph.textContent.trim() || paragraph.querySelectorAll('img').length !== 1) {
                return;
            }
            const nodes = [paragraph];
            let previous = paragraph.previousElementSibling;
            while (previous && /^H[1-6]$/.test(previous.tagName)) {
                nodes.unshift(previous);
                previous = previous.previousElementSibling;
            }
            if (nodes.length === 1) {
                return;
            }
            const group = document.createElement('div');
            group.className = 'mod_lessonmark-print-figure';
            nodes[0].before(group);
            nodes.forEach(node => group.appendChild(node));
            groups.push(group);
        });
    };
    const restore = () => {
        if (answers === null) {
            return;
        }
        answers.forEach(({element, open}) => { element.open = open; });
        groups.forEach(group => group.replaceWith(...group.childNodes));
        answers = null;
        groups = [];
    };
    window.addEventListener('beforeprint', prepare);
    window.addEventListener('afterprint', restore);
})();
