(function () {
    'use strict';

    var APP_ID = 'topictranslate-app';
    var BUTTON_SELECTOR = '[data-topictranslate-toggle]';
    var CONTENT_SELECTOR = '[data-topictranslate-content]';
    var STATUS_SELECTOR = '[data-topictranslate-status]';
    var CLOSE_SELECTOR = '[data-topictranslate-close]';
    var RESET_SELECTOR = '[data-topictranslate-reset]';
    var REPEAT_SELECTOR = '[data-topictranslate-repeat-last]';
    var STORAGE_KEY = 'topictranslate:last-language';
    var SCRIPT_ID = 'topictranslate-external-script';
    var EXCLUDED_CONTENT_SELECTOR = [
        'code',
        'pre',
        'kbd',
        'samp',
        '.codebox',
        '.syntaxbg',
        '.inline-attachment',
        '.mention',
        '[data-notranslate]'
    ].join(',');

    var app = document.getElementById(APP_ID);
    if (!app) {
        return;
    }

    var config = readConfig(app);
    var activeState = null;
    var detectedBrowserLanguage = null;
    var ownedGTranslateSettings = null;
    var ownsDocumentNotranslate = false;
    var gtranslateConflict = hasPreExistingGTranslate();
    var scriptState = {
        requested: false,
        loaded: false,
        failed: false,
        timer: null
    };

    if (!gtranslateConflict) {
        ownedGTranslateSettings = {
            default_language: config.defaultLanguage,
            native_language_names: config.nativeLanguageNames,
            detect_browser_language: config.detectBrowserLanguage,
            languages: config.languages,
            wrapper_selector: '.gtranslate_wrapper',
            flag_size: 16,
            switcher_horizontal_position: 'inline',
            flag_style: '3d'
        };
        window.gtranslateSettings = ownedGTranslateSettings;
    }

    init();

    function init() {
        if (!hasGTranslateConflict()) {
            enableDocumentTranslationIsolation();
        }
        document.addEventListener('click', onDocumentClick);
        document.addEventListener('keydown', onDocumentKeydown);
        applyColorScheme();
        watchAutomaticColorScheme();

        if (!config.rememberLanguage) {
            removeStoredLanguage();
        }

        updateActionButtons();

        if (!config.lazyLoad && !hasGTranslateConflict()) {
            requestExternalScript();
        }
    }

    function readConfig(element) {
        return {
            defaultLanguage: element.getAttribute('data-default-language') || 'en',
            languages: parseLanguages(element.getAttribute('data-languages')),
            nativeLanguageNames: element.getAttribute('data-native-language-names') === '1',
            detectBrowserLanguage: element.getAttribute('data-detect-browser-language') === '1',
            colorScheme: normalizeColorScheme(element.getAttribute('data-color-scheme')),
            lazyLoad: element.getAttribute('data-lazy-load') !== '0',
            rememberLanguage: element.getAttribute('data-remember-language') !== '0',
            cookieDomain: element.getAttribute('data-cookie-domain') || '',
            cookiePath: element.getAttribute('data-cookie-path') || '/',
            scriptSrc: element.getAttribute('data-script-src') || '',
            loadingLabel: element.getAttribute('data-loading-label') || 'Loading translator…',
            blockedLabel: element.getAttribute('data-blocked-label') || 'The translation widget was blocked.',
            conflictLabel: element.getAttribute('data-conflict-label') || 'Another GTranslate widget is already active on this page.',
            unavailableLabel: element.getAttribute('data-unavailable-label') || 'Translation service is unavailable.',
            resetDoneLabel: element.getAttribute('data-reset-done-label') || 'Original content restored.',
            useLastLabel: element.getAttribute('data-use-last-label') || 'Use last language',
            loadTimeoutMs: 8000
        };
    }

    function normalizeColorScheme(value) {
        return value === 'light' || value === 'dark' ? value : 'auto';
    }

    function applyColorScheme() {
        var resolvedScheme = config.colorScheme === 'auto'
            ? detectForumColorScheme()
            : config.colorScheme;

        app.setAttribute('data-resolved-color-scheme', resolvedScheme);
    }

    function detectForumColorScheme() {
        var candidates = [
            document.querySelector('.wrap'),
            document.getElementById('page-body'),
            document.body,
            document.documentElement
        ];

        for (var index = 0; index < candidates.length; index += 1) {
            var element = candidates[index];
            if (!element || !window.getComputedStyle) {
                continue;
            }

            var color = parseCssColor(window.getComputedStyle(element).backgroundColor);
            if (color && color.alpha >= 0.5) {
                return relativeLuminance(color) < 0.42 ? 'dark' : 'light';
            }
        }

        for (var textIndex = 0; textIndex < candidates.length; textIndex += 1) {
            var textElement = candidates[textIndex];
            if (!textElement || !window.getComputedStyle) {
                continue;
            }

            var textColor = parseCssColor(window.getComputedStyle(textElement).color);
            if (textColor && textColor.alpha >= 0.5) {
                var textLuminance = relativeLuminance(textColor);
                if (textLuminance >= 0.65) {
                    return 'dark';
                }
                if (textLuminance <= 0.35) {
                    return 'light';
                }
            }
        }

        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
            ? 'dark'
            : 'light';
    }

    function parseCssColor(value) {
        var match = String(value || '').match(/rgba?\(\s*([\d.]+)[,\s]+([\d.]+)[,\s]+([\d.]+)(?:\s*[,/]\s*([\d.]+))?\s*\)/i);
        if (!match) {
            return null;
        }

        return {
            red: Number(match[1]),
            green: Number(match[2]),
            blue: Number(match[3]),
            alpha: match[4] === undefined ? 1 : Number(match[4])
        };
    }

    function relativeLuminance(color) {
        var channels = [color.red, color.green, color.blue].map(function (channel) {
            var normalized = channel / 255;
            return normalized <= 0.03928
                ? normalized / 12.92
                : Math.pow((normalized + 0.055) / 1.055, 2.4);
        });

        return (0.2126 * channels[0]) + (0.7152 * channels[1]) + (0.0722 * channels[2]);
    }

    function watchAutomaticColorScheme() {
        if (config.colorScheme !== 'auto') {
            return;
        }

        if (window.matchMedia) {
            var colorPreference = window.matchMedia('(prefers-color-scheme: dark)');
            if (colorPreference.addEventListener) {
                colorPreference.addEventListener('change', applyColorScheme);
            } else if (colorPreference.addListener) {
                colorPreference.addListener(applyColorScheme);
            }
        }

        if (window.MutationObserver) {
            var observer = new window.MutationObserver(applyColorScheme);
            var observerOptions = {
                attributes: true,
                attributeFilter: ['class', 'style', 'data-theme', 'data-color-scheme']
            };
            observer.observe(document.documentElement, observerOptions);
            if (document.body) {
                observer.observe(document.body, observerOptions);
            }
            var themeSurface = document.querySelector('.wrap, #page-body');
            if (themeSurface && themeSurface !== document.body) {
                observer.observe(themeSurface, observerOptions);
            }
        }
    }

    function parseLanguages(value) {
        try {
            var parsed = JSON.parse(value || '[]');
            return Array.isArray(parsed) && parsed.length ? parsed : ['en'];
        } catch (error) {
            return ['en'];
        }
    }

    function onDocumentClick(event) {
        var toggle = closestElement(event.target, BUTTON_SELECTOR);
        if (toggle) {
            event.preventDefault();
            toggleTranslator(toggle);
            return;
        }

        var closeButton = closestElement(event.target, CLOSE_SELECTOR);
        if (closeButton) {
            event.preventDefault();
            closePopover(true);
            return;
        }

        var resetButton = closestElement(event.target, RESET_SELECTOR);
        if (resetButton) {
            event.preventDefault();
            resetActiveTranslation(true);
            return;
        }

        var repeatButton = closestElement(event.target, REPEAT_SELECTOR);
        if (repeatButton) {
            event.preventDefault();
            repeatLastLanguage();
            return;
        }

        if (!app.hidden && !app.contains(event.target)) {
            closePopover(false);
        }
    }

    function onDocumentKeydown(event) {
        if ((event.key === 'Escape' || event.key === 'Esc') && !app.hidden) {
            event.preventDefault();
            closePopover(true);
        }
    }

    function closestElement(target, selector) {
        return target && target.closest ? target.closest(selector) : null;
    }

    function toggleTranslator(button) {
        if (activeState && activeState.button === button && !app.hidden) {
            closePopover(true);
            return;
        }

        openTranslator(button);
    }

    function openTranslator(button) {
        var postArea = getPostArea(button);
        var item = button.closest('.topictranslate-item');

        if (!postArea || !item) {
            return;
        }

        if (activeState && activeState.button !== button) {
            resetActiveTranslation(false);
        }

        if (!activeState) {
            activeState = {
                button: button,
                original: postArea,
                clone: null,
                translated: false,
                conflict: false
            };
        }

        item.appendChild(app);
        applyColorScheme();
        app.hidden = false;
        app.setAttribute('aria-hidden', 'false');
        button.setAttribute('aria-expanded', 'true');
        button.classList.add('topictranslate-button--active');
        setActionsAvailable(true);

        if (hasGTranslateConflict()) {
            showGTranslateConflict();
            return;
        }

        showStatus(config.loadingLabel, false, true);
        updateActionButtons();

        if (!requestExternalScript()) {
            if (hasGTranslateConflict()) {
                showGTranslateConflict();
            } else {
                showStatus(config.unavailableLabel, true, false);
            }
            return;
        }

        waitForSelector(40);
    }

    function getPostArea(button) {
        var postId = button.getAttribute('data-post-id');
        if (postId) {
            return document.getElementById('post-text-' + postId);
        }

        var postBody = button.closest('.postbody');
        return postBody ? postBody.querySelector(CONTENT_SELECTOR) : null;
    }

    function createTranslationClone(original) {
        var clone = original.cloneNode(true);
        var postId = original.getAttribute('data-post-id') || String(Date.now());

        clone.id = 'post-translation-' + postId;
        clone.removeAttribute('data-topictranslate-content');
        clone.setAttribute('data-topictranslate-render', '1');
        clone.classList.remove('notranslate');
        clone.classList.add('translate', 'topictranslate-render');
        clone.setAttribute('translate', 'yes');
        clone.setAttribute('aria-hidden', 'true');
        clone.hidden = true;

        clone.querySelectorAll('[id]').forEach(function (element) {
            element.removeAttribute('id');
        });
        clone.querySelectorAll('label[for]').forEach(function (element) {
            element.removeAttribute('for');
        });
        clone.querySelectorAll('[aria-labelledby], [aria-describedby]').forEach(function (element) {
            element.removeAttribute('aria-labelledby');
            element.removeAttribute('aria-describedby');
        });
        clone.querySelectorAll(EXCLUDED_CONTENT_SELECTOR).forEach(markAsNotranslate);

        if (original.parentNode) {
            original.parentNode.insertBefore(clone, original.nextSibling);
        }

        return clone;
    }

    function markAsNotranslate(element) {
        element.classList.add('notranslate');
        element.setAttribute('translate', 'no');
    }

    function prepareActiveCloneForTranslation() {
        if (!activeState) {
            return false;
        }

        if (activeState.translated && activeState.clone && activeState.clone.parentNode) {
            activeState.clone.parentNode.removeChild(activeState.clone);
            activeState.clone = null;
        }

        if (!activeState.clone || !activeState.clone.parentNode) {
            activeState.clone = createTranslationClone(activeState.original);
        }

        activeState.original.hidden = true;
        activeState.original.setAttribute('aria-hidden', 'true');
        activeState.clone.hidden = false;
        activeState.clone.setAttribute('aria-hidden', 'false');
        activeState.translated = false;

        return true;
    }

    function requestExternalScript() {
        if (hasGTranslateConflict()) {
            return false;
        }

        if (hasTranslatorSelector()) {
            scriptState.loaded = true;
            scriptState.failed = false;
            return true;
        }

        if (scriptState.failed) {
            return false;
        }

        if (scriptState.requested) {
            return true;
        }

        if (!config.scriptSrc) {
            scriptState.failed = true;
            return false;
        }

        scriptState.requested = true;

        var script = document.createElement('script');
        script.id = SCRIPT_ID;
        script.setAttribute('data-topictranslate-owner', '1');
        script.src = config.scriptSrc;
        script.async = true;
        script.referrerPolicy = 'strict-origin-when-cross-origin';
        script.addEventListener('load', function () {
            scriptState.loaded = true;
            scriptState.failed = false;
            clearScriptTimeout();
        });
        script.addEventListener('error', function () {
            scriptState.loaded = false;
            scriptState.failed = true;
            clearScriptTimeout();
            showStatus(config.blockedLabel, true, false);
        });

        scriptState.timer = window.setTimeout(function () {
            if (!hasTranslatorSelector()) {
                scriptState.failed = true;
                showStatus(config.blockedLabel, true, false);
            }
        }, config.loadTimeoutMs);

        document.head.appendChild(script);
        return true;
    }

    function clearScriptTimeout() {
        if (scriptState.timer) {
            window.clearTimeout(scriptState.timer);
            scriptState.timer = null;
        }
    }

    function hasTranslatorSelector() {
        return !!app.querySelector('.gtranslate_wrapper select, select.gt_selector');
    }

    function hasPreExistingGTranslate() {
        return typeof window.gtranslateSettings !== 'undefined' || hasExternalGTranslateMarkup();
    }

    function hasGTranslateConflict() {
        if (gtranslateConflict) {
            releaseDocumentTranslationIsolation();
            return true;
        }

        if (!ownedGTranslateSettings || window.gtranslateSettings !== ownedGTranslateSettings) {
            markGTranslateConflict();
            return true;
        }

        if (hasExternalGTranslateMarkup()) {
            markGTranslateConflict();
            return true;
        }

        return false;
    }

    function markGTranslateConflict() {
        gtranslateConflict = true;
        releaseDocumentTranslationIsolation();
        if (activeState) {
            activeState.conflict = true;
        }
    }

    function enableDocumentTranslationIsolation() {
        if (!document.documentElement.classList.contains('notranslate')) {
            document.documentElement.classList.add('notranslate');
            ownsDocumentNotranslate = true;
        }
    }

    function releaseDocumentTranslationIsolation() {
        if (ownsDocumentNotranslate) {
            document.documentElement.classList.remove('notranslate');
            ownsDocumentNotranslate = false;
        }

        document.querySelectorAll(CONTENT_SELECTOR).forEach(function (content) {
            content.classList.remove('notranslate');
            content.removeAttribute('translate');
        });
    }

    function hasExternalGTranslateMarkup() {
        var widgetNodes = document.querySelectorAll('.gtranslate_wrapper, select.gt_selector');
        for (var index = 0; index < widgetNodes.length; index += 1) {
            if (!app.contains(widgetNodes[index])) {
                return true;
            }
        }

        var scripts = document.querySelectorAll('script[src]');
        for (var scriptIndex = 0; scriptIndex < scripts.length; scriptIndex += 1) {
            var script = scripts[scriptIndex];
            var source = String(script.getAttribute('src') || '');
            if (script.getAttribute('data-topictranslate-owner') !== '1' && /gtranslate\.(?:net|io)\//i.test(source)) {
                return true;
            }
        }

        return false;
    }

    function showGTranslateConflict() {
        markGTranslateConflict();
        setActionsAvailable(false);
        showStatus(config.conflictLabel, true, false);
    }

    function setActionsAvailable(isAvailable) {
        var actions = app.querySelector('.topictranslate-actions');
        if (actions) {
            actions.hidden = !isAvailable;
        }
    }

    function waitForSelector(attemptsRemaining) {
        var select = app.querySelector('.gtranslate_wrapper select, select.gt_selector');

        if (select) {
            bindSelector(select);
            clearStatus();
            updateActionButtons(select);
            applyDetectedBrowserLanguage(select, 10);
            window.setTimeout(function () {
                select.focus();
            }, 20);
            return;
        }

        if (scriptState.failed || attemptsRemaining <= 0) {
            showStatus(scriptState.failed ? config.blockedLabel : config.unavailableLabel, true, false);
            return;
        }

        showStatus(config.loadingLabel, false, true);
        window.setTimeout(function () {
            waitForSelector(attemptsRemaining - 1);
        }, 250);
    }

    function bindSelector(select) {
        if (select.getAttribute('data-topictranslate-bound') === '1') {
            return;
        }

        select.setAttribute('data-topictranslate-bound', '1');
        select.setAttribute('aria-label', app.querySelector('.gtranslate_wrapper').getAttribute('aria-label') || 'Language');
        select.addEventListener('change', onSelectorChange, true);
    }

    function applyDetectedBrowserLanguage(select, attemptsRemaining) {
        if (!config.detectBrowserLanguage || !activeState || activeState.translated || app.hidden) {
            return;
        }

        var languageCode = normalizeLanguageValue(select.value);
        var alreadyApplied = select.getAttribute('data-topictranslate-browser-language');

        if (languageCode && languageCode !== 'auto' && languageCode !== config.defaultLanguage) {
            detectedBrowserLanguage = languageCode;
            if (alreadyApplied !== languageCode) {
                select.setAttribute('data-topictranslate-browser-language', languageCode);
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
            return;
        }

        if (detectedBrowserLanguage && setLanguageOption(select, detectedBrowserLanguage)) {
            select.setAttribute('data-topictranslate-browser-language', detectedBrowserLanguage);
            select.dispatchEvent(new Event('change', { bubbles: true }));
            return;
        }

        if (attemptsRemaining > 0) {
            window.setTimeout(function () {
                applyDetectedBrowserLanguage(select, attemptsRemaining - 1);
            }, 100);
        }
    }

    function onSelectorChange(event) {
        var select = event.currentTarget;
        var languageCode = normalizeLanguageValue(select.value);

        if (languageCode === config.defaultLanguage || languageCode === 'auto' || !languageCode) {
            resetActiveTranslation(true);
            return;
        }

        if (!prepareActiveCloneForTranslation()) {
            return;
        }

        saveLastLanguage(languageCode, getSelectedOptionLabel(select));
        activeState.translated = true;
        markTranslatedState(true);
        showStatus(config.loadingLabel, false, true);
        updateActionButtons(select);

        window.setTimeout(function () {
            closePopover(true);
            resetSelectorToSource(select);
        }, 600);
    }

    function normalizeLanguageValue(value) {
        var normalized = String(value || '');
        return normalized.indexOf('|') !== -1 ? normalized.split('|')[1] : normalized;
    }

    function getSelectedOptionLabel(select) {
        if (!select || !select.options || select.selectedIndex < 0) {
            return '';
        }

        return String(select.options[select.selectedIndex].text || '').trim();
    }

    function resetSelectorToSource(select) {
        if (!select || !select.options) {
            return;
        }

        select.removeAttribute('data-topictranslate-browser-language');

        for (var index = 0; index < select.options.length; index += 1) {
            if (normalizeLanguageValue(select.options[index].value) === config.defaultLanguage) {
                select.selectedIndex = index;
                return;
            }
        }

        select.selectedIndex = 0;
    }

    function resetActiveTranslation(keepTarget) {
        if (!activeState) {
            if (!hasGTranslateConflict()) {
                clearTranslatorCookies();
                resetExternalArtifacts();
            }
            clearStatus();
            return;
        }

        var state = activeState;

        if (state.clone && state.clone.parentNode) {
            state.clone.parentNode.removeChild(state.clone);
        }

        state.original.hidden = false;
        state.original.removeAttribute('aria-hidden');
        state.button.classList.remove('topictranslate-button--translated');
        state.translated = false;
        state.clone = null;

        if (!state.conflict) {
            clearTranslatorCookies();
            resetExternalArtifacts();
            resetSelectorToSource(app.querySelector('.gtranslate_wrapper select, select.gt_selector'));
        }

        if (keepTarget) {
            showStatus(config.resetDoneLabel, false, false);
            state.button.classList.add('topictranslate-button--active');
            state.button.setAttribute('aria-expanded', app.hidden ? 'false' : 'true');
        } else {
            state.button.classList.remove('topictranslate-button--active');
            state.button.setAttribute('aria-expanded', 'false');
            activeState = null;
            closePopover(false);
        }

        updateActionButtons();
    }

    function markTranslatedState(isTranslated) {
        if (!activeState) {
            return;
        }

        activeState.button.classList.toggle('topictranslate-button--translated', !!isTranslated);
    }

    function closePopover(restoreFocus) {
        if (app.hidden) {
            return;
        }

        app.hidden = true;
        app.setAttribute('aria-hidden', 'true');
        clearStatus();

        document.querySelectorAll(BUTTON_SELECTOR).forEach(function (button) {
            button.classList.remove('topictranslate-button--active');
            button.setAttribute('aria-expanded', 'false');
        });

        if (restoreFocus && activeState && activeState.button) {
            activeState.button.focus();
        }
    }

    function showStatus(message, isError, isLoading) {
        var status = app.querySelector(STATUS_SELECTOR);
        if (!status) {
            return;
        }

        status.textContent = message;
        status.hidden = false;
        status.classList.toggle('topictranslate-status--error', !!isError);
        status.classList.toggle('topictranslate-status--loading', !isError && !!isLoading);
        app.classList.toggle('topictranslate-popover--loading', !isError && !!isLoading);
    }

    function clearStatus() {
        var status = app.querySelector(STATUS_SELECTOR);
        if (!status) {
            return;
        }

        status.textContent = '';
        status.hidden = true;
        status.classList.remove('topictranslate-status--error', 'topictranslate-status--loading');
        app.classList.remove('topictranslate-popover--loading');
    }

    function updateActionButtons(select) {
        var resetButton = app.querySelector(RESET_SELECTOR);
        var repeatButton = app.querySelector(REPEAT_SELECTOR);
        var lastLanguage = getLastLanguage();

        if (resetButton) {
            resetButton.hidden = !(activeState && activeState.translated);
        }

        if (!repeatButton) {
            return;
        }

        if (!lastLanguage || !lastLanguage.code || lastLanguage.code === config.defaultLanguage || (select && !hasLanguageOption(select, lastLanguage.code))) {
            repeatButton.hidden = true;
            repeatButton.textContent = '';
            return;
        }

        repeatButton.hidden = false;
        repeatButton.textContent = config.useLastLabel + ': ' + (lastLanguage.label || lastLanguage.code);
    }

    function hasLanguageOption(select, languageCode) {
        if (!select || !select.options) {
            return false;
        }

        for (var index = 0; index < select.options.length; index += 1) {
            if (normalizeLanguageValue(select.options[index].value) === languageCode) {
                return true;
            }
        }

        return false;
    }

    function setLanguageOption(select, languageCode) {
        if (!select || !select.options) {
            return false;
        }

        for (var index = 0; index < select.options.length; index += 1) {
            if (normalizeLanguageValue(select.options[index].value) === languageCode) {
                select.selectedIndex = index;
                return true;
            }
        }

        return false;
    }

    function repeatLastLanguage() {
        var select = app.querySelector('.gtranslate_wrapper select, select.gt_selector');
        var lastLanguage = getLastLanguage();

        if (!select || !lastLanguage || !lastLanguage.code || !setLanguageOption(select, lastLanguage.code)) {
            return;
        }

        select.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function saveLastLanguage(code, label) {
        if (!config.rememberLanguage || !code || code === config.defaultLanguage || code === 'auto') {
            return;
        }

        try {
            window.localStorage.setItem(STORAGE_KEY, JSON.stringify({
                code: code,
                label: label || code
            }));
        } catch (error) {
            // Storage may be unavailable in strict privacy modes.
        }
    }

    function getLastLanguage() {
        if (!config.rememberLanguage) {
            return null;
        }

        try {
            var raw = window.localStorage.getItem(STORAGE_KEY);
            var parsed = raw ? JSON.parse(raw) : null;
            return parsed && parsed.code ? parsed : null;
        } catch (error) {
            return null;
        }
    }

    function removeStoredLanguage() {
        try {
            window.localStorage.removeItem(STORAGE_KEY);
        } catch (error) {
            // Storage may be unavailable in strict privacy modes.
        }
    }

    function clearTranslatorCookies() {
        var cookieNames = ['googtrans', 'googtrans_ext'];
        var domains = uniqueValues([
            '',
            window.location.hostname,
            '.' + window.location.hostname,
            config.cookieDomain,
            config.cookieDomain && config.cookieDomain.charAt(0) === '.' ? config.cookieDomain.substring(1) : '.' + config.cookieDomain
        ]);
        var paths = uniqueValues(['/', config.cookiePath]);

        cookieNames.forEach(function (cookieName) {
            paths.forEach(function (path) {
                domains.forEach(function (domain) {
                    var cookie = cookieName + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; max-age=0; path=' + (path || '/') + '; SameSite=Lax;';
                    if (domain) {
                        cookie += ' domain=' + domain + ';';
                    }
                    document.cookie = cookie;
                });
            });
        });
    }

    function uniqueValues(values) {
        return values.filter(function (value, index, list) {
            return value !== null && value !== undefined && list.indexOf(value) === index;
        });
    }

    function resetExternalArtifacts() {
        document.documentElement.classList.remove('translated-ltr', 'translated-rtl');
        document.body.classList.remove('translated-ltr', 'translated-rtl');
        document.body.style.top = '';

        [
            '#goog-gt-tt',
            '.goog-te-spinner-pos',
            '.goog-te-balloon-frame',
            'iframe.goog-te-banner-frame'
        ].forEach(function (selector) {
            document.querySelectorAll(selector).forEach(function (node) {
                if (node.parentNode) {
                    node.parentNode.removeChild(node);
                }
            });
        });
    }
})();
