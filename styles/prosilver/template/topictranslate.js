(function () {
    'use strict';

    var APP_ID = 'topictranslate-app';
    var BUTTON_SELECTOR = '[data-topictranslate-toggle]';
    var CONTENT_SELECTOR = '[data-topictranslate-content]';
    var STATUS_SELECTOR = '[data-topictranslate-status]';
    var CLOSE_SELECTOR = '[data-topictranslate-close]';
    var RESET_SELECTOR = '[data-topictranslate-reset]';
    var REPEAT_SELECTOR = '[data-topictranslate-repeat-last]';
    var LANGUAGE_SELECTOR = '[data-topictranslate-language]';
    var PICKER_SELECTOR = '[data-topictranslate-picker]';
    var PICKER_TOGGLE_SELECTOR = '[data-topictranslate-picker-toggle]';
    var PICKER_MENU_SELECTOR = '[data-topictranslate-picker-menu]';
    var PICKER_OPTION_SELECTOR = '[data-topictranslate-picker-option]';
    var SERVICE_SELECTOR = '[data-topictranslate-service]';
    var STORAGE_KEY = 'topictranslate:last-language';
    var SCRIPT_ID = 'topictranslate-external-script';
    var GOOGLE_ELEMENT_ID = 'topictranslate-google-element';
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
    var translationRequestId = 0;
    var ownedGTranslateSettings = null;
    var ownsDocumentNotranslate = false;
    var gtranslateConflict = hasPreExistingGTranslate();
    var scriptState = {
        requested: false,
        loaded: false,
        failed: false,
        sourceIndex: 0,
        timer: null
    };

    if (!gtranslateConflict) {
        ownedGTranslateSettings = {
            default_language: config.defaultLanguage,
            native_language_names: config.nativeLanguageNames,
            detect_browser_language: config.detectBrowserLanguage,
            languages: config.languages,
            wrapper_selector: '.topictranslate-service',
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
        installGoogleInitCallback();

        var select = app.querySelector(LANGUAGE_SELECTOR);
        bindSelector(select);
        localizeNativeLanguageNames(select);
        initLanguagePicker(select);

        if (!config.rememberLanguage) {
            removeStoredLanguage();
        }

        updateActionButtons(select);

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
            fallbackScriptSrc: element.getAttribute('data-script-fallback-src') || '',
            loadingLabel: element.getAttribute('data-loading-label') || 'Loading translator…',
            blockedLabel: element.getAttribute('data-blocked-label') || 'The translation service was blocked.',
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

    function localizeNativeLanguageNames(select) {
        if (!config.nativeLanguageNames || !select || !window.Intl || !window.Intl.DisplayNames) {
            return;
        }

        Array.prototype.forEach.call(select.options, function (option) {
            var code = option.getAttribute('data-language-code');
            if (!code) {
                return;
            }

            var locale = code === 'iw' ? 'he' : code;
            try {
                var displayNames = new window.Intl.DisplayNames([locale], { type: 'language' });
                option.textContent = displayNames.of(locale) || option.textContent;
            } catch (error) {
                // Keep the server-provided label when Intl does not support a locale.
            }
        });
    }

    function initLanguagePicker(select) {
        if (!select) {
            return;
        }

        var picker = select.closest(PICKER_SELECTOR);
        var toggle = picker ? picker.querySelector(PICKER_TOGGLE_SELECTOR) : null;
        var menu = picker ? picker.querySelector(PICKER_MENU_SELECTOR) : null;
        if (!picker || !toggle || !menu) {
            return;
        }

        menu.innerHTML = '';
        Array.prototype.forEach.call(select.options, function (option, index) {
            var item = document.createElement('button');
            item.type = 'button';
            item.className = 'topictranslate-picker-option';
            item.setAttribute('role', 'option');
            item.setAttribute('tabindex', '-1');
            item.setAttribute('data-topictranslate-picker-option', '1');
            item.setAttribute('data-option-index', String(index));
            item.appendChild(createFlagElement(option.getAttribute('data-flag')));

            var label = document.createElement('span');
            label.className = 'topictranslate-picker-label';
            label.textContent = option.textContent;
            item.appendChild(label);
            menu.appendChild(item);
        });

        toggle.addEventListener('click', function () {
            if (menu.hidden) {
                openLanguagePicker(select);
            } else {
                closeLanguagePicker(select, false);
            }
        });
        toggle.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp' || event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openLanguagePicker(select);
            }
        });
        menu.addEventListener('click', function (event) {
            var item = closestElement(event.target, PICKER_OPTION_SELECTOR);
            if (item) {
                event.preventDefault();
                chooseLanguagePickerOption(select, item);
            }
        });
        menu.addEventListener('keydown', function (event) {
            handleLanguagePickerKeydown(event, select);
        });

        syncLanguagePicker(select);
    }

    function createFlagElement(flagCode) {
        var flag = document.createElement('span');
        var normalizedFlag = String(flagCode || 'un').toLowerCase();
        flag.className = 'topictranslate-flag topictranslate-flag--' + normalizedFlag;
        flag.setAttribute('aria-hidden', 'true');
        return flag;
    }

    function syncLanguagePicker(select) {
        if (!select || select.selectedIndex < 0) {
            return;
        }

        var picker = select.closest(PICKER_SELECTOR);
        var toggle = picker ? picker.querySelector(PICKER_TOGGLE_SELECTOR) : null;
        var menu = picker ? picker.querySelector(PICKER_MENU_SELECTOR) : null;
        var selectedOption = select.options[select.selectedIndex];
        if (!toggle || !menu || !selectedOption) {
            return;
        }

        toggle.innerHTML = '';
        toggle.appendChild(createFlagElement(selectedOption.getAttribute('data-flag')));

        var label = document.createElement('span');
        label.className = 'topictranslate-picker-label';
        label.textContent = selectedOption.textContent;
        toggle.appendChild(label);
        toggle.setAttribute('aria-label', (select.getAttribute('aria-label') || 'Language') + ': ' + selectedOption.textContent);

        var arrow = document.createElement('span');
        arrow.className = 'topictranslate-picker-arrow';
        arrow.setAttribute('aria-hidden', 'true');
        toggle.appendChild(arrow);

        menu.querySelectorAll(PICKER_OPTION_SELECTOR).forEach(function (item) {
            var isSelected = Number(item.getAttribute('data-option-index')) === select.selectedIndex;
            item.classList.toggle('topictranslate-picker-option--selected', isSelected);
            item.setAttribute('aria-selected', isSelected ? 'true' : 'false');
        });
    }

    function openLanguagePicker(select) {
        if (!select || select.disabled) {
            return;
        }

        var picker = select.closest(PICKER_SELECTOR);
        var toggle = picker ? picker.querySelector(PICKER_TOGGLE_SELECTOR) : null;
        var menu = picker ? picker.querySelector(PICKER_MENU_SELECTOR) : null;
        if (!picker || !toggle || !menu) {
            return;
        }

        menu.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
        picker.classList.add('topictranslate-language-picker--open');

        var selected = menu.querySelector('[aria-selected="true"]') || menu.querySelector(PICKER_OPTION_SELECTOR);
        if (selected) {
            selected.focus();
        }
    }

    function closeLanguagePicker(select, restoreFocus) {
        if (!select) {
            return;
        }

        var picker = select.closest(PICKER_SELECTOR);
        var toggle = picker ? picker.querySelector(PICKER_TOGGLE_SELECTOR) : null;
        var menu = picker ? picker.querySelector(PICKER_MENU_SELECTOR) : null;
        if (!picker || !toggle || !menu) {
            return;
        }

        menu.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
        picker.classList.remove('topictranslate-language-picker--open');
        if (restoreFocus) {
            toggle.focus();
        }
    }

    function chooseLanguagePickerOption(select, item) {
        var optionIndex = Number(item.getAttribute('data-option-index'));
        if (!select.options[optionIndex]) {
            return;
        }

        select.selectedIndex = optionIndex;
        syncLanguagePicker(select);
        closeLanguagePicker(select, true);
        dispatchChange(select);
    }

    function handleLanguagePickerKeydown(event, select) {
        var item = closestElement(event.target, PICKER_OPTION_SELECTOR);
        if (!item) {
            return;
        }

        var menu = item.closest(PICKER_MENU_SELECTOR);
        var items = menu ? Array.prototype.slice.call(menu.querySelectorAll(PICKER_OPTION_SELECTOR)) : [];
        var currentIndex = items.indexOf(item);
        var nextIndex = currentIndex;

        if (event.key === 'Escape') {
            event.preventDefault();
            event.stopPropagation();
            closeLanguagePicker(select, true);
            return;
        }
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            chooseLanguagePickerOption(select, item);
            return;
        }
        if (event.key === 'ArrowDown') {
            nextIndex = Math.min(items.length - 1, currentIndex + 1);
        } else if (event.key === 'ArrowUp') {
            nextIndex = Math.max(0, currentIndex - 1);
        } else if (event.key === 'Home') {
            nextIndex = 0;
        } else if (event.key === 'End') {
            nextIndex = items.length - 1;
        } else {
            return;
        }

        event.preventDefault();
        if (items[nextIndex]) {
            items[nextIndex].focus();
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

        var picker = app.querySelector(PICKER_SELECTOR);
        if (!app.hidden && picker && !picker.contains(event.target)) {
            closeLanguagePicker(app.querySelector(LANGUAGE_SELECTOR), false);
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

        var select = app.querySelector(LANGUAGE_SELECTOR);
        clearStatus();
        updateActionButtons(select);
        requestExternalScript();
        applyDetectedBrowserLanguage(select);

        window.setTimeout(function () {
            if (select && !app.hidden) {
                select.focus();
            }
        }, 20);
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

    function installGoogleInitCallback() {
        window.topicTranslateGoogleInit = function () {
            if (hasGTranslateConflict()) {
                return;
            }

            var host = document.getElementById(GOOGLE_ELEMENT_ID);
            if (!host || findGoogleSelector()) {
                scriptState.loaded = true;
                scriptState.failed = false;
                clearScriptTimeout();
                return;
            }

            if (!window.google || !window.google.translate || !window.google.translate.TranslateElement) {
                scriptState.failed = true;
                return;
            }

            if (host.getAttribute('data-topictranslate-initialized') === '1') {
                return;
            }

            host.setAttribute('data-topictranslate-initialized', '1');
            try {
                new window.google.translate.TranslateElement({
                    pageLanguage: config.defaultLanguage,
                    includedLanguages: config.languages.join(','),
                    autoDisplay: false
                }, GOOGLE_ELEMENT_ID);
                scriptState.loaded = true;
                scriptState.failed = false;
            } catch (error) {
                host.removeAttribute('data-topictranslate-initialized');
                scriptState.failed = true;
            }
        };
    }

    function requestExternalScript() {
        if (hasGTranslateConflict()) {
            return false;
        }

        if (findGoogleSelector()) {
            scriptState.loaded = true;
            scriptState.failed = false;
            clearScriptTimeout();
            return true;
        }

        if (window.google && window.google.translate && window.google.translate.TranslateElement) {
            scriptState.requested = true;
            window.topicTranslateGoogleInit();
            return true;
        }

        if (scriptState.requested) {
            return !scriptState.failed;
        }

        var sources = getTranslationScriptSources();
        if (!sources.length) {
            scriptState.failed = true;
            return false;
        }

        scriptState.sourceIndex = Math.min(scriptState.sourceIndex, sources.length - 1);
        loadTranslationScript(sources[scriptState.sourceIndex]);
        return true;
    }

    function getTranslationScriptSources() {
        return uniqueValues([config.scriptSrc, config.fallbackScriptSrc]).filter(function (source) {
            return !!source;
        });
    }

    function loadTranslationScript(source) {
        scriptState.requested = true;
        scriptState.failed = false;

        var script = document.createElement('script');
        script.id = SCRIPT_ID;
        script.setAttribute('data-topictranslate-owner', '1');
        script.src = source;
        script.async = true;
        script.referrerPolicy = 'strict-origin-when-cross-origin';
        script.addEventListener('load', function () {
            if (document.getElementById(SCRIPT_ID) !== script) {
                return;
            }
            scriptState.loaded = true;
            if (findGoogleSelector()) {
                scriptState.failed = false;
                clearScriptTimeout();
            }
        });
        script.addEventListener('error', function () {
            if (document.getElementById(SCRIPT_ID) === script) {
                retryTranslationScript();
            }
        });

        clearScriptTimeout();
        scriptState.timer = window.setTimeout(function () {
            if (!findGoogleSelector() && document.getElementById(SCRIPT_ID) === script) {
                retryTranslationScript();
            }
        }, config.loadTimeoutMs);

        document.head.appendChild(script);
    }

    function retryTranslationScript() {
        clearScriptTimeout();

        var currentScript = document.getElementById(SCRIPT_ID);
        if (currentScript && currentScript.parentNode) {
            currentScript.parentNode.removeChild(currentScript);
        }

        var host = document.getElementById(GOOGLE_ELEMENT_ID);
        if (host && !findGoogleSelector()) {
            host.innerHTML = '';
            host.removeAttribute('data-topictranslate-initialized');
        }

        var sources = getTranslationScriptSources();
        scriptState.sourceIndex += 1;
        scriptState.loaded = false;

        if (scriptState.sourceIndex < sources.length) {
            scriptState.requested = false;
            loadTranslationScript(sources[scriptState.sourceIndex]);
            return;
        }

        scriptState.requested = true;
        scriptState.failed = true;
    }

    function clearScriptTimeout() {
        if (scriptState.timer) {
            window.clearTimeout(scriptState.timer);
            scriptState.timer = null;
        }
    }

    function findGoogleSelector() {
        var service = app.querySelector(SERVICE_SELECTOR);
        return service ? service.querySelector('select.goog-te-combo') : null;
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
        var widgetNodes = document.querySelectorAll('.gtranslate_wrapper, select.gt_selector, select.goog-te-combo, #google_translate_element, #google_translate_element2');
        for (var index = 0; index < widgetNodes.length; index += 1) {
            if (!app.contains(widgetNodes[index])) {
                return true;
            }
        }

        var scripts = document.querySelectorAll('script[src]');
        for (var scriptIndex = 0; scriptIndex < scripts.length; scriptIndex += 1) {
            var script = scripts[scriptIndex];
            var source = String(script.getAttribute('src') || '');
            if (script.getAttribute('data-topictranslate-owner') !== '1' && (/gtranslate\.(?:net|io)\//i.test(source) || /translate\.(?:googleapis|google)\.com\/translate_a\/element\.js/i.test(source))) {
                return true;
            }
        }

        return false;
    }

    function showGTranslateConflict() {
        markGTranslateConflict();
        setActionsAvailable(false);
        var select = app.querySelector(LANGUAGE_SELECTOR);
        setLanguageSelectorDisabled(select, true);
        showStatus(config.conflictLabel, true, false);
    }

    function setActionsAvailable(isAvailable) {
        var actions = app.querySelector('.topictranslate-actions');
        if (actions) {
            actions.hidden = !isAvailable;
        }
    }

    function setLanguageSelectorDisabled(select, isDisabled) {
        if (!select) {
            return;
        }

        select.disabled = !!isDisabled;
        var picker = select.closest(PICKER_SELECTOR);
        var toggle = picker ? picker.querySelector(PICKER_TOGGLE_SELECTOR) : null;
        if (toggle) {
            toggle.disabled = !!isDisabled;
        }
        if (isDisabled) {
            closeLanguagePicker(select, false);
        }
    }

    function bindSelector(select) {
        if (!select || select.getAttribute('data-topictranslate-bound') === '1') {
            return;
        }

        select.setAttribute('data-topictranslate-bound', '1');
        select.addEventListener('change', onSelectorChange);
    }

    function applyDetectedBrowserLanguage(select) {
        if (!config.detectBrowserLanguage || !activeState || activeState.translated || app.hidden) {
            return;
        }

        var alreadyApplied = select.getAttribute('data-topictranslate-browser-language');
        if (!detectedBrowserLanguage) {
            detectedBrowserLanguage = detectBrowserLanguage();
        }

        if (detectedBrowserLanguage && setLanguageOption(select, detectedBrowserLanguage)) {
            if (alreadyApplied !== detectedBrowserLanguage) {
                select.setAttribute('data-topictranslate-browser-language', detectedBrowserLanguage);
                dispatchChange(select);
            }
        }
    }

    function detectBrowserLanguage() {
        if (!window.navigator) {
            return null;
        }

        var language = String(window.navigator.language || window.navigator.userLanguage || '').toLowerCase();
        if (language === 'zh' || language === 'zh-cn') {
            return 'zh-CN';
        }
        if (language === 'zh-tw' || language === 'zh-hk') {
            return 'zh-TW';
        }
        if (language === 'he') {
            return 'iw';
        }

        return language.substring(0, 2) || null;
    }

    function onSelectorChange(event) {
        var select = event.currentTarget;
        syncLanguagePicker(select);
        var languageCode = normalizeLanguageValue(select.value);

        if (languageCode === config.defaultLanguage || languageCode === 'auto' || !languageCode) {
            resetActiveTranslation(true);
            return;
        }

        if (!prepareActiveCloneForTranslation()) {
            return;
        }

        translationRequestId += 1;
        var requestId = translationRequestId;
        setLanguageSelectorDisabled(select, true);
        showStatus(config.loadingLabel, false, true);
        updateActionButtons(select);

        if (!requestExternalScript()) {
            failTranslationRequest(requestId, select, hasGTranslateConflict() ? config.conflictLabel : config.blockedLabel);
            return;
        }

        waitForTranslator(80, requestId, select, languageCode, getSelectedOptionLabel(select));
    }

    function waitForTranslator(attemptsRemaining, requestId, select, languageCode, languageLabel) {
        if (requestId !== translationRequestId || !activeState) {
            return;
        }

        var googleSelect = findGoogleSelector();
        if (googleSelect && hasLanguageOption(googleSelect, languageCode)) {
            applyTranslationRequest(requestId, select, googleSelect, languageCode, languageLabel);
            return;
        }

        if (scriptState.failed || attemptsRemaining <= 0) {
            failTranslationRequest(requestId, select, scriptState.failed ? config.blockedLabel : config.unavailableLabel);
            return;
        }

        window.setTimeout(function () {
            waitForTranslator(attemptsRemaining - 1, requestId, select, languageCode, languageLabel);
        }, 250);
    }

    function applyTranslationRequest(requestId, select, googleSelect, languageCode, languageLabel) {
        if (requestId !== translationRequestId || !activeState || !setLanguageOption(googleSelect, languageCode)) {
            return;
        }

        dispatchChange(googleSelect);
        dispatchChange(googleSelect);

        saveLastLanguage(languageCode, languageLabel);
        activeState.translated = true;
        markTranslatedState(true);
        setLanguageSelectorDisabled(select, false);
        clearStatus();
        updateActionButtons(select);

        window.setTimeout(function () {
            if (requestId === translationRequestId) {
                closePopover(true);
                resetSelectorToSource(select);
            }
        }, 600);
    }

    function failTranslationRequest(requestId, select, message) {
        if (requestId !== translationRequestId) {
            return;
        }

        if (activeState) {
            if (activeState.clone && activeState.clone.parentNode) {
                activeState.clone.parentNode.removeChild(activeState.clone);
            }
            activeState.clone = null;
            activeState.translated = false;
            activeState.original.hidden = false;
            activeState.original.removeAttribute('aria-hidden');
            markTranslatedState(false);
        }

        setLanguageSelectorDisabled(select, false);
        resetSelectorToSource(select);
        updateActionButtons(select);
        showStatus(message, true, false);
    }

    function dispatchChange(element) {
        var event;
        if (typeof window.Event === 'function') {
            event = new window.Event('change', { bubbles: true });
        } else {
            event = document.createEvent('HTMLEvents');
            event.initEvent('change', true, true);
        }
        element.dispatchEvent(event);
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
                syncLanguagePicker(select);
                return;
            }
        }

        select.selectedIndex = 0;
        syncLanguagePicker(select);
    }

    function resetActiveTranslation(keepTarget) {
        translationRequestId += 1;
        var languageSelect = app.querySelector(LANGUAGE_SELECTOR);
        setLanguageSelectorDisabled(languageSelect, false);

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
            resetSelectorToSource(languageSelect);
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

        updateActionButtons(languageSelect);
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

        var languageSelect = app.querySelector(LANGUAGE_SELECTOR);
        if (languageSelect && languageSelect.disabled && activeState && !activeState.translated) {
            translationRequestId += 1;
            if (activeState.clone && activeState.clone.parentNode) {
                activeState.clone.parentNode.removeChild(activeState.clone);
            }
            activeState.clone = null;
            activeState.original.hidden = false;
            activeState.original.removeAttribute('aria-hidden');
            setLanguageSelectorDisabled(languageSelect, false);
            resetSelectorToSource(languageSelect);
        }

        closeLanguagePicker(languageSelect, false);
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
        var select = app.querySelector(LANGUAGE_SELECTOR);
        var lastLanguage = getLastLanguage();

        if (!select || !lastLanguage || !lastLanguage.code || !setLanguageOption(select, lastLanguage.code)) {
            return;
        }

        syncLanguagePicker(select);
        dispatchChange(select);
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
