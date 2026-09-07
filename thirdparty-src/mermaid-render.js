(function (global) {
    'use strict';

    var initialized = false;
    var diagramCounter = 0;
    var mermaidWaitMs = 5000;
    var mermaidPollMs = 50;

    function initialize() {
        if (initialized) {
            return true;
        }

        if (!global.mermaid || typeof global.mermaid.initialize !== 'function') {
            return false;
        }

        global.mermaid.initialize({
            startOnLoad: false,
            securityLevel: 'strict',
            suppressErrorRendering: true,
            maxTextSize: 50000,
            maxEdges: 500,
            htmlLabels: false,
            flowchart: {
                htmlLabels: false,
                useMaxWidth: true
            }
        });

        initialized = true;
        return true;
    }

    function waitForMermaid() {
        if (initialize()) {
            return Promise.resolve(true);
        }

        return new Promise(function (resolve) {
            var started = Date.now();
            var timer = global.setInterval(function () {
                if (initialize()) {
                    global.clearInterval(timer);
                    resolve(true);
                    return;
                }
                if (Date.now() - started >= mermaidWaitMs) {
                    global.clearInterval(timer);
                    resolve(false);
                }
            }, mermaidPollMs);
        });
    }

    function renderCodeBlock(code) {
        if (code.dataset.ozmdMermaidState) {
            return Promise.resolve();
        }

        var pre = code.parentElement;
        if (!pre) {
            return Promise.resolve();
        }

        code.dataset.ozmdMermaidState = 'rendering';
        diagramCounter += 1;

        return global.mermaid
            .render('ozmd-mermaid-' + diagramCounter, code.textContent || '')
            .then(function (result) {
                var figure = document.createElement('figure');
                figure.className = 'ozmd-mermaid';

                // Mermaid creates this SVG with securityLevel strict.
                figure.innerHTML = result.svg;
                pre.replaceWith(figure);

                if (typeof result.bindFunctions === 'function') {
                    result.bindFunctions(figure);
                }
            })
            .catch(function () {
                code.dataset.ozmdMermaidState = 'error';
                pre.classList.add('ozmd-mermaid-error');
            });
    }

    function render(root) {
        return waitForMermaid().then(function (available) {
            if (!available) {
                return;
            }

            var scope = root && typeof root.querySelectorAll === 'function'
                ? root
                : document;
            var blocks = Array.prototype.slice.call(
                scope.querySelectorAll('pre > code.language-mermaid')
            );

            return blocks.reduce(function (promise, block) {
                return promise.then(function () {
                    return renderCodeBlock(block);
                });
            }, Promise.resolve());
        });
    }

    global.ozmdRenderMermaid = render;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            render(document);
        });
    } else {
        render(document);
    }
}(window));
