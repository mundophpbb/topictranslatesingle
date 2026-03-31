(function () {
    'use strict';

    var bootstrapConfig = window.topicTranslateConfig || null;

    if (!bootstrapConfig) {
        return;
    }

    var ORIGINAL_POST_SNAPSHOTS = new Map();
    var CONTENT_SELECTOR = '[data-topictranslate-content]';
    var WRAPPER_SELECTOR = '.gtranslate_wrapper';
    var BUTTON_SELECTOR = '[data-topictranslate-toggle]';
    var RESET_SELECTOR = '[data-topictranslate-reset]';
    var REPEAT_SELECTOR = '[data-topictranslate-repeat-last]';
    var SCRIPT_ID = 'topictranslate-external-script';
    var LAST_LANGUAGE_STORAGE_KEY = 'topictranslate:last-language';
    var activeWrapper = null;

    var scriptState = {
        loaded: false,
        failed: false,
        requested: false,
        timedOut: false,
        timer: null
    };

    var config = {
        defaultLanguage: bootstrapConfig.defaultLanguage || 'en',
        nativeLanguageNames: !!bootstrapConfig.nativeLanguageNames,
        detectBrowserLanguage: !!bootstrapConfig.detectBrowserLanguage,
        compatibilityMode: !!bootstrapConfig.compatibilityMode,
        languages: Array.isArray(bootstrapConfig.languages) ? bootstrapConfig.languages : parseLanguages(''),
        translateLabel: bootstrapConfig.translateLabel || 'Translate',
        loadingLabel: bootstrapConfig.loadingLabel || 'Loading translator…',
        blockedLabel: bootstrapConfig.blockedLabel || 'The translation widget did not load. Check browser blocking, CSP or network filters and try again.',
        resetLabel: bootstrapConfig.resetLabel || 'Return to original language',
        unavailableLabel: bootstrapConfig.unavailableLabel || 'Translation service is unavailable right now.',
        resetDoneLabel: bootstrapConfig.resetDoneLabel || 'Original content restored.',
        poweredLabel: bootstrapConfig.poweredLabel || 'Translated by',
        useLastLanguageLabel: bootstrapConfig.useLastLanguageLabel || 'Use last language',
        wrapperSelector: WRAPPER_SELECTOR,
        loadTimeoutMs: 7000,
        flag_size: 16,
        switcher_horizontal_position: 'inline',
        flag_style: '3d'
    };

    window.gtranslateSettings = {
        default_language: config.defaultLanguage,
        native_language_names: config.nativeLanguageNames,
        detect_browser_language: config.detectBrowserLanguage,
        languages: config.languages,
        wrapper_selector: config.wrapperSelector,
        flag_size: config.flag_size,
        switcher_horizontal_position: config.switcher_horizontal_position,
        flag_style: config.flag_style
    };

    function initTopicTranslate() {
        cacheOriginalPosts();
        buildWrapperLabels();
        initExternalScriptState();
        document.addEventListener('click', onDocumentClick);
        document.addEventListener('keydown', onDocumentKeydown);

        if (!config.compatibilityMode) {
            requestExternalScript();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTopicTranslate);
    } else {
        initTopicTranslate();
    }

    function parseLanguages(value) {
        try {
            var parsed = JSON.parse(value || '["en"]');
            return Array.isArray(parsed) && parsed.length ? parsed : ['en'];
        } catch (error) {
            return ['en'];
        }
    }

    function getExternalScript() {
        return document.getElementById(SCRIPT_ID);
    }

    function initExternalScriptState() {
        var script = getExternalScript();

        if (!script) {
            scriptState.failed = true;
            return;
        }

        attachExternalScriptListeners(script);

        if (document.querySelector('select.gt_selector, .gtranslate_wrapper select')) {
            scriptState.loaded = true;
        }

        if (script.getAttribute('src')) {
            scriptState.requested = true;
            startExternalScriptTimeout();
        }
    }

    function attachExternalScriptListeners(script) {
        if (!script || script.getAttribute('data-topictranslate-bound') === '1') {
            return;
        }

        script.setAttribute('data-topictranslate-bound', '1');

        script.addEventListener('load', function () {
            script.setAttribute('data-topictranslate-loaded', '1');
            scriptState.loaded = true;
            scriptState.failed = false;
            scriptState.timedOut = false;
            clearExternalScriptTimeout();
        });

        script.addEventListener('error', function () {
            scriptState.failed = true;
            scriptState.loaded = false;
            clearExternalScriptTimeout();
            announceExternalFailure();
        });
    }

    function startExternalScriptTimeout() {
        clearExternalScriptTimeout();
        scriptState.timedOut = false;

        scriptState.timer = window.setTimeout(function () {
            syncExternalScriptState();
            if (!scriptState.loaded) {
                scriptState.timedOut = true;
            }
        }, config.loadTimeoutMs);
    }

    function clearExternalScriptTimeout() {
        if (scriptState.timer) {
            window.clearTimeout(scriptState.timer);
            scriptState.timer = null;
        }
    }

    function hasTranslatorRuntime() {
        return typeof window.doGTranslate === 'function'
            || !!(window.google && window.google.translate)
            || !!document.querySelector('select.gt_selector, .gtranslate_wrapper select');
    }

    function syncExternalScriptState() {
        var script = getExternalScript();

        if (hasTranslatorRuntime()) {
            scriptState.loaded = true;
            scriptState.failed = false;
            scriptState.timedOut = false;
            clearExternalScriptTimeout();
            return;
        }

        if (!script) {
            return;
        }

        var readyState = String(script.readyState || '').toLowerCase();
        if (script.getAttribute('data-topictranslate-loaded') === '1' || readyState === 'loaded' || readyState === 'complete') {
            scriptState.loaded = true;
            scriptState.failed = false;
            scriptState.timedOut = false;
            clearExternalScriptTimeout();
        }
    }

    function requestExternalScript() {
        var script = getExternalScript();

        if (!script) {
            scriptState.failed = true;
            return false;
        }

        attachExternalScriptListeners(script);
        syncExternalScriptState();

        if (scriptState.loaded) {
            return true;
        }

        if (!scriptState.requested) {
            var src = script.getAttribute('data-src') || script.getAttribute('src');

            if (!src) {
                scriptState.failed = true;
                return false;
            }

            scriptState.requested = true;
            scriptState.failed = false;
            scriptState.timedOut = false;
            startExternalScriptTimeout();
            script.setAttribute('src', src);
        }

        return true;
    }

    function cacheOriginalPosts() {
        document.querySelectorAll(CONTENT_SELECTOR).forEach(function (element) {
            if (!ORIGINAL_POST_SNAPSHOTS.has(element.id)) {
                ORIGINAL_POST_SNAPSHOTS.set(element.id, element.cloneNode(true));
            }
        });
    }

    function buildWrapperLabels() {
        document.querySelectorAll(WRAPPER_SELECTOR).forEach(ensureWrapperChrome);
    }

    function ensureWrapperChrome(wrapper) {
        if (!wrapper.querySelector('.gt_label_translate')) {
            var label = document.createElement('div');
            label.className = 'gt_label_translate gt-box';
            label.textContent = config.translateLabel;
            wrapper.insertBefore(label, wrapper.firstChild);
        }

        if (!wrapper.querySelector('.gt_status')) {
            var status = document.createElement('div');
            status.className = 'gt_status';
            status.hidden = true;
            wrapper.appendChild(status);
        }

        if (!wrapper.querySelector('.gt_repeat_last_button')) {
            var repeatButton = document.createElement('button');
            repeatButton.type = 'button';
            repeatButton.className = 'button button-secondary gt_repeat_last_button';
            repeatButton.setAttribute('data-topictranslate-repeat-last', '1');
            repeatButton.hidden = true;
            wrapper.appendChild(repeatButton);
        }

        if (!wrapper.querySelector(RESET_SELECTOR)) {
            var resetButton = document.createElement('button');
            resetButton.type = 'button';
            resetButton.className = 'button button-secondary gt_reset_button';
            resetButton.setAttribute('data-topictranslate-reset', '1');
            resetButton.textContent = config.resetLabel;
            wrapper.appendChild(resetButton);
        }

        if (!wrapper.querySelector('.gt_credit_inline')) {
            var credit = document.createElement('div');
            credit.className = 'gt_credit_inline';
            credit.innerHTML = '<a href="https://gtranslate.io" target="_blank" rel="nofollow noopener">' + config.poweredLabel + ' Google Translate</a>';
            wrapper.appendChild(credit);
        }

        updateRepeatButton(wrapper);
    }

    function onDocumentClick(event) {
        var toggle = event.target.closest(BUTTON_SELECTOR);
        if (toggle) {
            event.preventDefault();
            openTranslator(toggle);
            return;
        }

        var repeatButton = event.target.closest(REPEAT_SELECTOR);
        if (repeatButton) {
            event.preventDefault();
            applyLastLanguage(repeatButton.closest(WRAPPER_SELECTOR));
            return;
        }

        var resetButton = event.target.closest(RESET_SELECTOR);
        if (resetButton) {
            event.preventDefault();
            resetTranslator(resetButton.closest(WRAPPER_SELECTOR));
            return;
        }

        if (!event.target.closest(WRAPPER_SELECTOR)) {
            hideAllWrappers();
        }
    }

    function onDocumentKeydown(event) {
        if (event.key === 'Escape' || event.key === 'Esc') {
            hideAllWrappers();
        }
    }

    function openTranslator(button) {
        var wrapper = button.parentNode.querySelector(WRAPPER_SELECTOR);
        var isSameOpenWrapper = wrapper && activeWrapper === wrapper && !wrapper.hidden;

        if (isSameOpenWrapper) {
            hideAllWrappers();
            return;
        }

        hideAllWrappers();
        restoreAllPosts();

        var postArea = getPostAreaFromButton(button);
        if (postArea) {
            markPostForTranslation(postArea);
        }

        if (!wrapper) {
            return;
        }

        ensureWrapperChrome(wrapper);
        wrapper.hidden = false;
        wrapper.style.display = 'block';
        wrapper.setAttribute('aria-hidden', 'false');
        activeWrapper = wrapper;
        button.setAttribute('aria-expanded', 'true');
        button.classList.add('topictranslate-button--active');
        showStatus(wrapper, config.loadingLabel, false, true);

        if (!requestExternalScript()) {
            showStatus(wrapper, config.unavailableLabel, true, false);
            return;
        }

        waitForTranslatorSelect(wrapper, config.compatibilityMode ? 48 : 40);
    }

    function waitForTranslatorSelect(wrapper, attemptsRemaining) {
        syncExternalScriptState();

        var select = wrapper.querySelector('select.gt_selector, select');

        if (select) {
            ensureWrapperChrome(wrapper);
            bindSelector(wrapper, select);
            prepareSelectorState(wrapper, select);
            clearStatus(wrapper);
            window.setTimeout(function () {
                select.focus();
            }, 20);
            return;
        }

        if (attemptsRemaining <= 0) {
            syncExternalScriptState();
            showStatus(wrapper, scriptState.failed ? config.blockedLabel : config.unavailableLabel, true, false);
            return;
        }

        showStatus(wrapper, config.loadingLabel, false, true);
        window.setTimeout(function () {
            waitForTranslatorSelect(wrapper, attemptsRemaining - 1);
        }, 250);
    }

    function prepareSelectorState(wrapper, select) {
        var savedLanguage = getLastLanguageCode();
        updateRepeatButton(wrapper, select);

        if (!savedLanguage || isSelectCurrentlyTranslated(select) || !hasOptionForLanguage(select, savedLanguage)) {
            return;
        }

        setSelectToLanguage(select, savedLanguage);
    }

    function bindSelector(wrapper, select) {
        if (select.dataset.topictranslateBound === '1') {
            return;
        }

        select.dataset.topictranslateBound = '1';
        select.addEventListener('change', function () {
            var targetLanguage = normalizeLanguageValue(this.value);
            if (targetLanguage === config.defaultLanguage || targetLanguage === 'auto') {
                resetTranslator(wrapper);
                return;
            }

            saveLastLanguage(targetLanguage, getSelectedOptionLabel(this));
            updateRepeatButton(wrapper, this);
            markWrapperTranslated(wrapper, true, targetLanguage);
            showStatus(wrapper, config.loadingLabel, false, true);
            window.setTimeout(function () {
                wrapper.hidden = true;
                wrapper.style.display = 'none';
                wrapper.setAttribute('aria-hidden', 'true');
                clearStatus(wrapper);
                activeWrapper = null;
                var toggleButton = getToggleButtonForWrapper(wrapper);
                if (toggleButton) {
                    toggleButton.classList.remove('topictranslate-button--active');
                    toggleButton.setAttribute('aria-expanded', 'false');
                }
            }, 400);
        });
    }

    function getSelectedOptionLabel(select) {
        if (!select || !select.options || select.selectedIndex < 0) {
            return '';
        }

        return String(select.options[select.selectedIndex].text || '').trim();
    }

    function getPostAreaFromButton(button) {
        var postBody = button.closest('.postbody');
        return postBody ? postBody.querySelector(CONTENT_SELECTOR) : null;
    }

    function markPostForTranslation(postArea) {
        if (!ORIGINAL_POST_SNAPSHOTS.has(postArea.id)) {
            ORIGINAL_POST_SNAPSHOTS.set(postArea.id, postArea.cloneNode(true));
        }

        postArea.classList.remove('notranslate');
        postArea.classList.add('translate');
        postArea.setAttribute('translate', 'yes');

        var item = postArea.closest('.post') || postArea.closest('li');
        if (item) {
            item.classList.add('topictranslate-post--active');
        }
    }

    function restoreAllPosts() {
        document.querySelectorAll(CONTENT_SELECTOR).forEach(function (element) {
            restorePostNode(element);
        });

        document.querySelectorAll('.topictranslate-post--active').forEach(function (element) {
            element.classList.remove('topictranslate-post--active');
        });

        clearTranslatedStates();
    }

    function restorePostNode(element) {
        if (!element || !element.id || !ORIGINAL_POST_SNAPSHOTS.has(element.id)) {
            if (element) {
                element.classList.add('notranslate');
                element.classList.remove('translate');
                element.setAttribute('translate', 'no');
            }
            return element;
        }

        var originalNode = ORIGINAL_POST_SNAPSHOTS.get(element.id).cloneNode(true);
        originalNode.classList.add('notranslate');
        originalNode.classList.remove('translate');
        originalNode.setAttribute('translate', 'no');

        if (element.parentNode) {
            element.parentNode.replaceChild(originalNode, element);
        }

        return originalNode;
    }

    function clearTranslatedStates() {
        document.querySelectorAll(WRAPPER_SELECTOR).forEach(function (wrapper) {
            wrapper.classList.remove('gtranslate_wrapper--translated');
            wrapper.removeAttribute('data-topictranslate-language');
        });

        document.querySelectorAll(BUTTON_SELECTOR).forEach(function (button) {
            button.classList.remove('topictranslate-button--translated');
        });
    }

    function hideAllWrappers() {
        document.querySelectorAll(WRAPPER_SELECTOR).forEach(function (wrapper) {
            wrapper.hidden = true;
            wrapper.style.display = 'none';
            wrapper.setAttribute('aria-hidden', 'true');
            clearStatus(wrapper);
            wrapper.classList.remove('gtranslate_wrapper--open');
        });

        document.querySelectorAll(BUTTON_SELECTOR).forEach(function (button) {
            button.setAttribute('aria-expanded', 'false');
            button.classList.remove('topictranslate-button--active');
        });

        activeWrapper = null;
    }

    function announceExternalFailure() {
        syncExternalScriptState();
        if (scriptState.loaded) {
            return;
        }

        document.querySelectorAll(WRAPPER_SELECTOR).forEach(function (wrapper) {
            if (!wrapper.hidden) {
                showStatus(wrapper, config.blockedLabel, true, false);
            }
        });
    }

    function showStatus(wrapper, message, isError, isLoading) {
        if (!wrapper) {
            return;
        }

        var status = wrapper.querySelector('.gt_status');
        if (!status) {
            return;
        }

        status.textContent = message;
        status.hidden = false;
        status.classList.toggle('gt_status--error', !!isError);
        status.classList.toggle('gt_status--loading', !isError && !!isLoading);
        wrapper.classList.toggle('gtranslate_wrapper--loading', !isError && !!isLoading);
    }

    function clearStatus(wrapper) {
        if (!wrapper) {
            return;
        }

        var status = wrapper.querySelector('.gt_status');
        if (!status) {
            return;
        }

        status.textContent = '';
        status.hidden = true;
        status.classList.remove('gt_status--error');
        status.classList.remove('gt_status--loading');
        wrapper.classList.remove('gtranslate_wrapper--loading');
    }

    function clearTranslatorCookies() {
        var hostname = window.location.hostname;
        var cookies = ['googtrans', 'googtrans_ext'];
        var hostParts = hostname.split('.');
        var baseDomain = hostParts.length >= 2 ? '.' + hostParts.slice(-2).join('.') : hostname;
        var domains = [hostname, '.' + hostname, baseDomain];

        cookies.forEach(function (cookieName) {
            domains.forEach(function (domain) {
                document.cookie = cookieName + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=' + domain + ';';
            });

            document.cookie = cookieName + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        });
    }

    function clearGoogleTranslateArtifacts() {
        document.documentElement.classList.remove('translated-ltr', 'translated-rtl');
        document.body.classList.remove('translated-ltr', 'translated-rtl');
        document.body.style.top = '';

        [
            '#goog-gt-tt',
            '.goog-te-spinner-pos',
            '.goog-te-balloon-frame',
            '.skiptranslate iframe',
            'iframe.goog-te-banner-frame'
        ].forEach(function (selector) {
            document.querySelectorAll(selector).forEach(function (node) {
                if (node && node.parentNode) {
                    node.parentNode.removeChild(node);
                }
            });
        });

        document.querySelectorAll('.skiptranslate').forEach(function (node) {
            if (node.classList.contains('gtranslate_wrapper')) {
                return;
            }

            if (node.parentNode) {
                node.parentNode.removeChild(node);
            }
        });
    }

    function resetSelectors() {
        document.querySelectorAll('select.gt_selector, .gtranslate_wrapper select').forEach(function (select) {
            if (!select || !select.options || !select.options.length) {
                return;
            }

            for (var index = 0; index < select.options.length; index += 1) {
                var optionValue = select.options[index].value || '';
                var targetLanguage = normalizeLanguageValue(optionValue);
                if (targetLanguage === config.defaultLanguage || targetLanguage === 'auto') {
                    select.selectedIndex = index;
                    return;
                }
            }

            select.selectedIndex = 0;
        });
    }

    function resetTranslator(wrapper) {
        clearTranslatorCookies();
        clearGoogleTranslateArtifacts();
        restoreAllPosts();
        resetSelectors();
        hideAllWrappers();
        markWrapperTranslated(wrapper, false);

        if (wrapper) {
            wrapper.hidden = false;
            wrapper.style.display = 'block';
            wrapper.setAttribute('aria-hidden', 'false');
            activeWrapper = wrapper;
            showStatus(wrapper, config.resetDoneLabel, false, false);
            updateRepeatButton(wrapper);
            var toggleButton = getToggleButtonForWrapper(wrapper);
            if (toggleButton) {
                toggleButton.classList.add('topictranslate-button--active');
                toggleButton.setAttribute('aria-expanded', 'true');
            }
        }
    }

    function getToggleButtonForWrapper(wrapper) {
        var item = wrapper ? wrapper.closest('.topictranslate-item') : null;
        return item ? item.querySelector(BUTTON_SELECTOR) : null;
    }

    function normalizeLanguageValue(value) {
        value = String(value || '');
        return value.indexOf('|') !== -1 ? value.split('|')[1] : value;
    }

    function hasOptionForLanguage(select, languageCode) {
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

    function setSelectToLanguage(select, languageCode) {
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

    function isSelectCurrentlyTranslated(select) {
        var selectedLabel = normalizeLanguageValue(select ? select.value : '');
        return !!selectedLabel && selectedLabel !== config.defaultLanguage && selectedLabel !== 'auto';
    }

    function saveLastLanguage(languageCode, languageLabel) {
        if (!window.localStorage || !languageCode || languageCode === config.defaultLanguage || languageCode === 'auto') {
            return;
        }

        try {
            window.localStorage.setItem(LAST_LANGUAGE_STORAGE_KEY, JSON.stringify({
                code: languageCode,
                label: languageLabel || languageCode
            }));
        } catch (error) {
            // Ignore storage errors.
        }
    }

    function getLastLanguageData() {
        if (!window.localStorage) {
            return null;
        }

        try {
            var raw = window.localStorage.getItem(LAST_LANGUAGE_STORAGE_KEY);
            if (!raw) {
                return null;
            }

            var parsed = JSON.parse(raw);
            if (!parsed || !parsed.code) {
                return null;
            }

            return parsed;
        } catch (error) {
            return null;
        }
    }

    function getLastLanguageCode() {
        var data = getLastLanguageData();
        return data ? data.code : '';
    }

    function updateRepeatButton(wrapper, select) {
        if (!wrapper) {
            return;
        }

        var repeatButton = wrapper.querySelector('.gt_repeat_last_button');
        if (!repeatButton) {
            return;
        }

        var lastLanguage = getLastLanguageData();
        if (!lastLanguage || !lastLanguage.code || lastLanguage.code === config.defaultLanguage) {
            repeatButton.hidden = true;
            repeatButton.textContent = '';
            return;
        }

        if (select && !hasOptionForLanguage(select, lastLanguage.code)) {
            repeatButton.hidden = true;
            repeatButton.textContent = '';
            return;
        }

        repeatButton.hidden = false;
        repeatButton.textContent = config.useLastLanguageLabel + ': ' + (lastLanguage.label || lastLanguage.code);
    }

    function applyLastLanguage(wrapper) {
        var lastLanguage = getLastLanguageData();
        var select = wrapper ? wrapper.querySelector('select.gt_selector, select') : null;

        if (!wrapper || !select || !lastLanguage || !lastLanguage.code) {
            return;
        }

        if (!setSelectToLanguage(select, lastLanguage.code)) {
            return;
        }

        markWrapperTranslated(wrapper, true, lastLanguage.code);
        select.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function markWrapperTranslated(wrapper, isTranslated, languageCode) {
        if (!wrapper) {
            return;
        }

        wrapper.classList.toggle('gtranslate_wrapper--translated', !!isTranslated);
        if (isTranslated && languageCode) {
            wrapper.setAttribute('data-topictranslate-language', languageCode);
        } else {
            wrapper.removeAttribute('data-topictranslate-language');
        }

        var toggleButton = getToggleButtonForWrapper(wrapper);
        if (!toggleButton) {
            return;
        }

        toggleButton.classList.toggle('topictranslate-button--translated', !!isTranslated);
        if (!isTranslated) {
            toggleButton.classList.remove('topictranslate-button--active');
        }
    }

    window.resetarTradutor = resetTranslator;
})();
