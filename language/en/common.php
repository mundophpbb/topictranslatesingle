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
    'TOPIC_TRANSLATE_TITLE'    => 'Translate this post',
    'TOPIC_TRANSLATE_RESET'    => 'Return to original language',
    'TOPIC_TRANSLATE_POWERED'  => 'Translated by',
    'TRANSLATE'                => 'Translate',
]);