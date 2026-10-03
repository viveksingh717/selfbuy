/**
 * "Processing..." state for admin forms that submit with a normal page load
 * (profile, password, settings, login...). Loaded by both admin layouts.
 *
 * AJAX forms are skipped: their handlers call e.preventDefault() and already
 * manage their own button. The check runs on the next tick so it sees that
 * decision whichever handler ran first - and so the clicked button is only
 * disabled after the browser has read its name/value into the request.
 *
 * Opt out on a form with: <form data-no-loading> - needed for a POST form that
 * returns a file download, since the page never reloads to reset the button.
 */
(function () {
    'use strict';

    var LOADING_HTML = '<i class="fa fa-spinner fa-spin mr-1"></i> Processing...';

    function setLoading(button) {
        if (button.dataset.loadingOriginal !== undefined) {
            return;
        }
        var isInput = button.tagName === 'INPUT';
        button.dataset.loadingOriginal = isInput ? button.value : button.innerHTML;
        button.style.minWidth = button.offsetWidth + 'px'; // stop the button jumping in size
        if (isInput) {
            button.value = 'Processing...';
        } else {
            button.innerHTML = LOADING_HTML;
        }
        button.disabled = true;
    }

    function restore(button) {
        if (button.dataset.loadingOriginal === undefined) {
            return;
        }
        if (button.tagName === 'INPUT') {
            button.value = button.dataset.loadingOriginal;
        } else {
            button.innerHTML = button.dataset.loadingOriginal;
        }
        delete button.dataset.loadingOriginal;
        button.style.minWidth = '';
        button.disabled = false;
    }

    function submitButtons(form) {
        var inside = form.querySelectorAll('button[type="submit"], button:not([type]), input[type="submit"]');
        var outside = form.id ? document.querySelectorAll('[form="' + form.id + '"][type="submit"]') : [];
        return Array.prototype.slice.call(inside).concat(Array.prototype.slice.call(outside));
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;
        var submitter = e.submitter;

        setTimeout(function () {
            if (e.defaultPrevented) return;                                  // AJAX form - handled elsewhere
            if ((form.getAttribute('method') || 'get').toLowerCase() === 'get') return; // filters / search
            if (form.target === '_blank' || form.hasAttribute('data-no-loading')) return;

            var buttons = submitButtons(form);
            if (!buttons.length) return;

            // Spinner on the clicked button, the others just disabled - no double submits.
            var main = submitter && buttons.indexOf(submitter) !== -1 ? submitter : buttons[0];
            buttons.forEach(function (button) {
                if (button === main) {
                    setLoading(button);
                } else if (!button.disabled) {
                    button.dataset.loadingOriginal = button.tagName === 'INPUT' ? button.value : button.innerHTML;
                    button.disabled = true;
                }
            });
        }, 0);
    });

    // Back/forward cache can restore a page with the buttons still disabled.
    window.addEventListener('pageshow', function (e) {
        if (!e.persisted) return;
        document.querySelectorAll('[data-loading-original]').forEach(restore);
    });
})();
