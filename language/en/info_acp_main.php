<?php
/**
 * Translation file for the ACP module of the Topic Translate Single extension.
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
    'ACP_TOPICTRANSLATESINGLE_TITLE'                  => 'Topic Translate Single',
    'ACP_TOPICTRANSLATESINGLE'                        => 'Settings',
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE'       => 'Default Language',
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE_EXPLAIN' => 'Default language for translation (English is used as the base language).',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES'              => 'Available Languages',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_EXPLAIN'      => 'Select the languages to display in the translator menu (hold Ctrl/Cmd to select multiple).',
    'ACP_TOPICTRANSLATESINGLE_SETTING_SAVED'          => 'Settings have been saved successfully!',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_TIP'          => 'The most common languages are pre-selected by default. Most forums use fewer than 15 languages. Less common or rare languages are listed at the end.',
]);