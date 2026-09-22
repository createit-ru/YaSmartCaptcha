/**
 * YaSmartCaptcha — minimal JS API for the invisible Yandex SmartCaptcha.
 *
 * Renders an invisible widget for every [data-yasmartcaptcha] container and
 * gives the site two calls. Submitting the form is up to the site:
 *
 *   form.addEventListener('submit', async (e) => {
 *       e.preventDefault();
 *       await YaSmartCaptcha.execute(form);   // the widget itself fills <input name="smart-token">
 *       // ...send the form any way you like, then:
 *       YaSmartCaptcha.reset(form);           // the token is single-use
 *   });
 *
 * The Yandex script must be loaded with ?render=onload&onload=YaSmartCaptchaInit.
 * The widget creates the hidden <input name="smart-token"> itself, inside the
 * container — do not add one in your markup, it would create a duplicate field.
 */
(function (window, document) {
    'use strict';

    var widgets = new Map(); // form -> {id, resolve}

    window.YaSmartCaptchaInit = function () {
        document.querySelectorAll('[data-yasmartcaptcha]').forEach(function (container) {
            var form = container.closest('form');
            var widget = {};
            widget.id = window.smartCaptcha.render(container, {
                sitekey: container.dataset.sitekey,
                invisible: true,
                hideShield: 'hideShield' in container.dataset,
                test: 'test' in container.dataset,
                callback: function (token) {
                    // The widget already put the token into its own hidden
                    // input inside the container, nothing to fill here.
                    if (widget.resolve) {
                        widget.resolve(token);
                    }
                }
            });
            widgets.set(form, widget);
        });
    };

    window.YaSmartCaptcha = {
        /** Runs the check, resolves with the token (it is also put into the form). */
        execute: function (form) {
            return new Promise(function (resolve) {
                var widget = widgets.get(form);
                widget.resolve = resolve;
                window.smartCaptcha.execute(widget.id);
            });
        },

        /** Resets the widget (it clears its own hidden input). */
        reset: function (form) {
            window.smartCaptcha.reset(widgets.get(form).id);
        }
    };
})(window, document);
