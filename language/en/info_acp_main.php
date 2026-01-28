<?php
/**
 * Language file for the ACP module of the Topic Translate Single extension.
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
    'ACP_TOPICTRANSLATESINGLE_TITLE' => 'Topic Translation',
    'ACP_TOPICTRANSLATESINGLE' => 'Settings',
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE' => 'Default Language (default_language)',
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE_EXPLAIN' => 'Default language (English as base).',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES' => 'Available Languages (languages)',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_EXPLAIN' => 'Select multiple languages for the menu (Ctrl+click).',
    'ACP_TOPICTRANSLATESINGLE_SETTING_SAVED' => 'Settings saved successfully!',
]);