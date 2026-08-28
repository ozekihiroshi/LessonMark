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
 * Dedicated Markdown editor with a save-free server preview.
 *
 * @module     mod_lessonmark/editor
 * @copyright  2026 Hiroshi Ozeki
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {watchForm} from 'core_form/changechecker';
import {highlight} from './syntax-highlighter';

const DEBOUNCE_MS = 400;

/**
 * Initialise one LessonMark editor.
 *
 * @param {Object} config Editor configuration.
 */
export const init = config => {
    const source = document.querySelector(config.sourceSelector);
    const container = document.querySelector(config.containerSelector);
    if (!source || !container) {
        return;
    }

    const sourceField = source.closest('.fitem');
    const preview = container.querySelector('[data-region="preview-content"]');
    const status = container.querySelector('[data-region="preview-status"]');
    const editButton = container.querySelector('[data-action="show-editor"]');
    const previewButton = container.querySelector('[data-action="show-preview"]');
    const refreshButton = container.querySelector('[data-action="refresh-preview"]');
    const shell = document.createElement('div');
    shell.className = 'mod_lessonmark-editor__panes';

    sourceField.parentNode.insertBefore(shell, sourceField);
    shell.append(sourceField, container);
    sourceField.classList.add('mod_lessonmark-editor__source');
    watchForm(source);

    let timer = null;
    let requestNumber = 0;

    const setMode = mode => {
        const showPreview = mode === 'preview';
        shell.dataset.mobileMode = mode;
        editButton.classList.toggle('is-active', !showPreview);
        previewButton.classList.toggle('is-active', showPreview);
        editButton.setAttribute('aria-selected', String(!showPreview));
        previewButton.setAttribute('aria-selected', String(showPreview));
    };

    const render = async() => {
        const currentRequest = ++requestNumber;
        status.textContent = config.strings.loading;
        refreshButton.disabled = true;
        const body = new URLSearchParams({
            sesskey: config.sesskey,
            markdownsource: source.value,
            cmid: String(config.cmid),
            courseid: String(config.courseid),
        });

        try {
            const response = await fetch(config.endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'},
                body,
            });
            const result = await response.json();
            if (currentRequest !== requestNumber) {
                return;
            }
            if (!response.ok || typeof result.html !== 'string') {
                throw new Error('Preview request failed');
            }
            if (result.html) {
                preview.innerHTML = result.html;
                preview.classList.remove('text-muted');
            } else {
                preview.textContent = config.strings.empty;
                preview.classList.add('text-muted');
            }
            highlight(preview);
            status.textContent = config.strings.ready;
        } catch (error) {
            if (currentRequest === requestNumber) {
                status.textContent = config.strings.error;
            }
        } finally {
            if (currentRequest === requestNumber) {
                refreshButton.disabled = false;
            }
        }
    };

    const scheduleRender = () => {
        window.clearTimeout(timer);
        timer = window.setTimeout(render, DEBOUNCE_MS);
    };

    source.addEventListener('input', scheduleRender);
    refreshButton.addEventListener('click', render);
    editButton.addEventListener('click', () => setMode('edit'));
    previewButton.addEventListener('click', () => {
        setMode('preview');
        render();
    });
    setMode('edit');
    render();
};
