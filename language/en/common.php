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
    'TOPIC_TRANSLATE_USE_LAST' => 'Use last language',
    'TRANSLATE'                => 'Translate',
    'TOPIC_TRANSLATE_SERVICE_UNAVAILABLE' => 'Translation service is unavailable right now. Please try again in a moment.',
    'TOPIC_TRANSLATE_RESET_DONE' => 'Original content restored.',
    'TOPIC_TRANSLATE_LOADING' => 'Loading translator…',
    'TOPIC_TRANSLATE_WIDGET_BLOCKED' => 'The translation widget did not load. Check browser blocking, CSP or network filters and try again.',
]);