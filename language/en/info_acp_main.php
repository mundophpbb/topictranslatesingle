<?php
/**
 * Translation file for the Topic Translate Single extension ACP module.
 * Language: English (en).
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
    'ACP_TOPICTRANSLATESINGLE_TITLE'             => 'Topic Translation',
    'ACP_TOPICTRANSLATESINGLE'                   => 'Settings',
    
    // Toggle keys
    'ACP_TTS_NATIVE_NAMES'                       => 'Display native names',
    'ACP_TTS_NATIVE_NAMES_EXPLAIN'               => 'If enabled, languages will appear in their own script (e.g., Español instead of Spanish).',
    
    'ACP_TTS_DETECT_BROWSER'                     => 'Auto-detect browser language',
    'ACP_TTS_DETECT_BROWSER_EXPLAIN'             => 'Tries to automatically translate the content to the visitor\'s primary language when opening the translator.',

    // Language Settings
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE'         => 'Default Language (default_language)',
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE_EXPLAIN' => 'The language Google will use as the base for translation.',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES'                => 'Available Languages (languages)',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_EXPLAIN'        => 'Select the languages that will appear in the selection menu.',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_TIP'            => 'Tip: Hold Ctrl to select multiple items. Most common languages are at the top; less common/rare ones are at the end of the list.',
    
    'ACP_TOPICTRANSLATESINGLE_SETTING_SAVED'            => 'Settings saved successfully!',
]);