/* ==========================================================================
   Admin theme settings persistence

   The right-hand "Settings" panel toggles (Night Mode, Header Dark, Sidebar
   Dark, Gradient, RTL, Box Layout, font, ...) only lived for the current page
   in the stock template. This script stores each choice in localStorage and
   re-applies it on every load so the preferences stick.

   Body-level classes are also applied by a tiny inline bootstrap right after
   <body> opens (see layouts/app.blade.php) to avoid a flash before this file
   runs.
   ========================================================================== */
(function () {
    "use strict";

    var STORAGE_KEY = 'selfbuy_admin_settings';

    // panel checkbox class -> { where to put the class, which class, default state }
    var TOGGLES = {
        'btn-darkmode':    { target: 'body',                   cls: 'dark-mode',   def: false },
        'btn-fixnavbar':   { target: '#page_top',              cls: 'sticky-top',  def: false },
        'btn-pageheader':  { target: '#page_top',              cls: 'top_dark',    def: true  },
        'btn-min_sidebar': { target: '#header_top',            cls: 'dark',        def: false },
        'btn-sidebar':     { target: 'body',                   cls: 'sidebar_dark', def: false },
        'btn-iconcolor':   { target: 'body',                   cls: 'iconcolor',   def: false },
        'btn-gradient':    { target: 'body',                   cls: 'gradient',    def: false },
        'btn-boxshadow':   { target: '.card, .btn, .progress', cls: 'box_shadow',  def: false },
        'btn-rtl':         { target: 'body',                   cls: 'rtl',         def: false },
        'btn-boxlayout':   { target: 'body',                   cls: 'boxlayout',   def: false }
    };

    var FONTS = ['font-opensans', 'font-montserrat', 'font-roboto'];
    var DEFAULT_FONT = 'font-montserrat';

    function read() {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || {}; }
        catch (e) { return {}; }
    }
    function write(cfg) {
        try { localStorage.setItem(STORAGE_KEY, JSON.stringify(cfg)); } catch (e) {}
    }
    function enabled(cfg, key, def) {
        return Object.prototype.hasOwnProperty.call(cfg, key) ? !!cfg[key] : def;
    }
    function each(selector, fn) {
        Array.prototype.forEach.call(document.querySelectorAll(selector), fn);
    }

    // Shared with the inline bootstrap in app.blade.php.
    window.SelfBuySettings = {
        STORAGE_KEY: STORAGE_KEY,
        FONTS: FONTS,
        DEFAULT_FONT: DEFAULT_FONT,
        applyBody: function () {
            var cfg = read();
            var body = document.body;

            var font = FONTS.indexOf(cfg.font) > -1 ? cfg.font : DEFAULT_FONT;
            FONTS.forEach(function (f) { body.classList.remove(f); });
            body.classList.add(font);

            Object.keys(TOGGLES).forEach(function (key) {
                var t = TOGGLES[key];
                if (t.target === 'body') {
                    body.classList.toggle(t.cls, enabled(cfg, key, t.def));
                }
            });
        }
    };

    function applyAll() {
        var cfg = read();

        window.SelfBuySettings.applyBody();

        Object.keys(TOGGLES).forEach(function (key) {
            var t = TOGGLES[key];
            var on = enabled(cfg, key, t.def);

            if (t.target !== 'body') {
                each(t.target, function (el) { el.classList.toggle(t.cls, on); });
            }
            each('.setting_switch .' + key, function (input) { input.checked = on; });
        });

        var font = FONTS.indexOf(cfg.font) > -1 ? cfg.font : DEFAULT_FONT;
        var radio = document.querySelector('.font_setting input[value="' + font + '"]');
        if (radio) { radio.checked = true; }
    }

    function bind() {
        Object.keys(TOGGLES).forEach(function (key) {
            each('.setting_switch .' + key, function (input) {
                input.addEventListener('change', function () {
                    var cfg = read();
                    cfg[key] = input.checked;
                    write(cfg);

                    // core.js already handles body-level classes; make sure the
                    // element-level targets follow too (and any re-rendered nodes).
                    var t = TOGGLES[key];
                    if (t.target !== 'body') {
                        each(t.target, function (el) { el.classList.toggle(t.cls, input.checked); });
                    }
                });
            });
        });

        each('.font_setting input[name="font"]', function (radio) {
            radio.addEventListener('change', function () {
                if (!radio.checked) { return; }
                var cfg = read();
                cfg.font = radio.value;
                write(cfg);
            });
        });
    }

    // On the first ever visit, persist the defaults so that later turning OFF a
    // toggle that defaults ON (e.g. "Header Dark") is remembered correctly.
    if (localStorage.getItem(STORAGE_KEY) === null) {
        var seed = { font: DEFAULT_FONT };
        Object.keys(TOGGLES).forEach(function (k) { seed[k] = TOGGLES[k].def; });
        write(seed);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { applyAll(); bind(); });
    } else {
        applyAll();
        bind();
    }
})();
