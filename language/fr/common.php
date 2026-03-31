<?php
/**
 * Translation file for the ACP module of the Topic Translate Single extension.
 * Language: French (fr).
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
    'TOPIC_TRANSLATE_TITLE'    => 'Traduire ce message',
    'TOPIC_TRANSLATE_RESET'    => 'Retour à la langue originale',
    'TOPIC_TRANSLATE_POWERED'  => 'Traduction par',
    'TOPIC_TRANSLATE_USE_LAST' => 'Utiliser la dernière langue',
    'TRANSLATE'                => 'Traduire',
    'TOPIC_TRANSLATE_SERVICE_UNAVAILABLE' => 'Le service de traduction est indisponible pour le moment. Veuillez réessayer dans un instant.',
    'TOPIC_TRANSLATE_RESET_DONE' => 'Le contenu original a été restauré.',
]);