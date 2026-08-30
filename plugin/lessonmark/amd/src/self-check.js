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
 * Stores ungraded LessonMark working answers in the current browser.
 *
 * @module     mod_lessonmark/self-check
 * @copyright  2026 Hiroshi Ozeki
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Reads the learner-controlled value from one self-check block.
 *
 * @param {HTMLElement} container Self-check container.
 * @returns {{type: String, value: String}} Serializable state.
 */
const readValue = container => {
    const textarea = container.querySelector('textarea[data-self-check-input="response"]');
    if (textarea) {
        return {type: 'response', value: textarea.value};
    }
    const checked = container.querySelector('input[data-self-check-input="choice"]:checked');
    return {type: 'choice', value: checked ? checked.value : ''};
};

/**
 * Restores a previously saved value without submitting it to Moodle.
 *
 * @param {HTMLElement} container Self-check container.
 * @param {Object|null} state Previously saved state.
 */
const restoreValue = (container, state) => {
    if (!state || typeof state.value !== 'string') {
        return;
    }
    if (state.type === 'response') {
        const textarea = container.querySelector('textarea[data-self-check-input="response"]');
        if (textarea) {
            textarea.value = state.value;
        }
        return;
    }
    const choices = container.querySelectorAll('input[data-self-check-input="choice"]');
    choices.forEach(choice => {
        choice.checked = choice.value === state.value;
    });
};

/** Browser storage wrapper that leaves controls usable when storage is blocked. */
const storage = {
    get(key) {
        try {
            return JSON.parse(window.localStorage.getItem(key));
        } catch {
            return null;
        }
    },
    set(key, value) {
        try {
            window.localStorage.setItem(key, JSON.stringify(value));
        } catch {
            // The control remains usable when browser storage is unavailable.
        }
    },
    remove(key) {
        try {
            window.localStorage.removeItem(key);
        } catch {
            // The visible control can still be cleared.
        }
    },
};

/**
 * Initialises browser-local persistence for rendered self-check blocks.
 *
 * @param {Object} config Initialisation configuration.
 * @param {Number} config.cmid Course-module ID.
 * @param {Number} config.userId Moodle user ID.
 */
export const init = config => {
    const root = document.querySelector('.mod_lessonmark-content');
    if (!root) {
        return;
    }
    root.querySelectorAll('[data-self-check]').forEach(container => {
        const key = [
            'mod_lessonmark',
            'selfcheck',
            String(config.userId),
            String(config.cmid),
            container.dataset.selfCheck,
        ].join(':');
        restoreValue(container, storage.get(key));

        container.addEventListener('input', () => storage.set(key, readValue(container)));
        container.addEventListener('change', () => storage.set(key, readValue(container)));
        const clear = container.querySelector('[data-action="clear-self-check"]');
        if (clear) {
            clear.addEventListener('click', () => {
                const textarea = container.querySelector('textarea[data-self-check-input="response"]');
                if (textarea) {
                    textarea.value = '';
                    textarea.focus();
                }
                container.querySelectorAll('input[data-self-check-input="choice"]').forEach(choice => {
                    choice.checked = false;
                });
                storage.remove(key);
            });
        }
    });
};
