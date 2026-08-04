<?php
/**
 * ACP language file for Topic Translate Single.
 */
if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = [];
}

$lang = array_merge($lang, [
    'ACP_TOPICTRANSLATESINGLE_TITLE' => 'Topic Translation',
    'ACP_TOPICTRANSLATESINGLE' => 'Settings',
    'ACP_TTS_INTRO' => 'Configure per-post translation. The original post remains untouched and the external service is loaded only when needed by default.',

    'ACP_TTS_LANGUAGE_SECTION' => 'Languages',
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE' => 'Original forum language',
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE_EXPLAIN' => 'Source language of the posts. This is independent from the translation destination languages.',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES' => 'Translation destination languages',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_EXPLAIN' => 'Select the languages visitors may choose in the translator.',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_TIP' => 'Use the search and selection buttons to manage the list.',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_REQUIRED' => 'Select at least one valid translation destination language.',
    'ACP_TTS_SEARCH_LANGUAGE' => 'Search languages…',
    'ACP_TTS_LANGUAGE_ACTIONS' => 'Language selection actions',
    'ACP_TTS_SELECT_POPULAR' => 'Select popular',
    'ACP_TTS_SELECT_ALL' => 'Select all',
    'ACP_TTS_SELECT_NONE' => 'Select none',
    'ACP_TTS_POPULAR_LANGUAGES' => 'Popular languages',
    'ACP_TTS_OTHER_LANGUAGES' => 'Other languages',
    'ACP_TTS_NATIVE_NAMES' => 'Display native names',
    'ACP_TTS_NATIVE_NAMES_EXPLAIN' => 'Display language names in their own writing system when supported by the widget.',
    'ACP_TTS_DETECT_BROWSER' => 'Detect browser language',
    'ACP_TTS_DETECT_BROWSER_EXPLAIN' => 'Allows the widget to suggest the visitor’s browser language. Disabled by default for predictable behavior.',

    'ACP_TTS_APPEARANCE_SECTION' => 'Appearance',
    'ACP_TTS_COLOR_SCHEME' => 'Translator color scheme',
    'ACP_TTS_COLOR_SCHEME_EXPLAIN' => 'Automatic mode detects the forum background and falls back to the browser preference. You may also force a light or dark palette.',
    'ACP_TTS_COLOR_SCHEME_AUTO' => 'Automatic',
    'ACP_TTS_COLOR_SCHEME_LIGHT' => 'Light',
    'ACP_TTS_COLOR_SCHEME_DARK' => 'Dark',

    'ACP_TTS_FORUM_SECTION' => 'Forum availability',
    'ACP_TTS_FORUM_MODE' => 'Forum list behavior',
    'ACP_TTS_FORUM_MODE_EXPLAIN' => 'Choose whether the selected forums are the only allowed forums or the forums where translation is blocked.',
    'ACP_TTS_FORUM_MODE_INCLUDE' => 'Enable only in selected forums',
    'ACP_TTS_FORUM_MODE_EXCLUDE' => 'Enable everywhere except selected forums',
    'ACP_TOPICTRANSLATESINGLE_ENABLED_FORUMS' => 'Forums',
    'ACP_TOPICTRANSLATESINGLE_ENABLED_FORUMS_EXPLAIN' => 'Select forums by name. If no forum is selected, translation remains available in all topic forums.',
    'ACP_TTS_SEARCH_FORUM' => 'Search forums…',
    'ACP_TTS_FORUM_LANGUAGES' => 'Original language by forum',
    'ACP_TTS_FORUM_LANGUAGES_EXPLAIN' => 'Set a different source language only for forums whose predominant language differs from the global setting. The global language is automatically offered as a translation destination in those forums.',
    'ACP_TTS_USE_GLOBAL_LANGUAGE' => 'Use global: %s',

    'ACP_TTS_PRIVACY_SECTION' => 'Performance and privacy',
    'ACP_TTS_LAZY_LOAD' => 'Load only after a click',
    'ACP_TTS_LAZY_LOAD_EXPLAIN' => 'Recommended. The external translation engine is requested only after a visitor opens the translator.',
    'ACP_TTS_REMEMBER_LANGUAGE' => 'Remember the last language',
    'ACP_TTS_REMEMBER_LANGUAGE_EXPLAIN' => 'Stores the last selected translation language in the visitor’s local browser storage.',
    'ACP_TTS_EXTERNAL_SERVICE_NOTICE' => 'The integration is based on the GTranslate widget and uses external Google Translate resources. When the translator is used, page content and technical request data may be processed by third parties. Review your privacy notice before enabling this feature in private forums.',

    'ACP_TOPICTRANSLATESINGLE_SETTING_SAVED' => 'Settings saved successfully!',
]);
