// This file is part of Moodle - https://moodle.org/
// Licensed under the GNU GPL v3 or later: https://www.gnu.org/copyleft/gpl.html
/**
 * Progressive enhancement for classroom presentation.
 * @copyright 2026 Hiroshi Ozeki
 */
(function() {
    'use strict';
    const init = () => {
        const root = document.querySelector('.mod_lessonmark-presentation');
        if (!root) {
            return;
        }
        const slides = Array.from(root.querySelectorAll('.mod_lessonmark-slide'));
        const previous = root.querySelector('[data-presentation-action="previous"]');
        const next = root.querySelector('[data-presentation-action="next"]');
        const fullscreen = root.querySelector('[data-presentation-action="fullscreen"]');
        const status = root.querySelector('[data-presentation-status]');
        let current = 0;
        const show = (index, focus = false) => {
            current = Math.max(0, Math.min(index, slides.length - 1));
            slides.forEach((slide, i) => {
                slide.hidden = i !== current;
            });
            previous.disabled = current === 0;
            next.disabled = current === slides.length - 1;
            status.textContent = `${current + 1} / ${slides.length}`;
            if (focus) {
                slides[current].focus();
            }
        };
        previous.addEventListener('click', () => show(current - 1, true));
        next.addEventListener('click', () => show(current + 1, true));
        root.addEventListener('keydown', event => {
            if (event.altKey || event.ctrlKey || event.metaKey || event.shiftKey ||
                    event.target.closest('input, textarea, select, button, a, [contenteditable="true"]')) {
                return;
            }
            const destinations = {ArrowLeft: current - 1, ArrowRight: current + 1, Home: 0, End: slides.length - 1};
            if (Object.prototype.hasOwnProperty.call(destinations, event.key)) {
                event.preventDefault();
                show(destinations[event.key], true);
            }
        });
        fullscreen.hidden = !root.requestFullscreen;
        fullscreen.addEventListener('click', async() => {
            try {
                if (document.fullscreenElement) {
                    await document.exitFullscreen();
                } else {
                    await root.requestFullscreen();
                }
            } catch (error) {
                // The ordinary presentation stays usable if fullscreen is denied.
                fullscreen.disabled = true;
            }
        });
        // Mermaid uses its own off-screen measurement; do not rerender author source here.
        show(0);
        slides[0].focus({preventScroll: true});
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, {once: true});
    } else {
        init();
    }
}());
