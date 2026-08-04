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
    'TOPIC_TRANSLATE_CLOSE'     => 'Fermer le traducteur',
    'TOPIC_TRANSLATE_LANGUAGE_SELECTOR' => 'Langue de traduction',
    'TRANSLATE'                => 'Traduire',
    'TOPIC_TRANSLATE_SERVICE_UNAVAILABLE' => 'Le service de traduction est indisponible pour le moment. Veuillez réessayer dans un instant.',
    'TOPIC_TRANSLATE_RESET_DONE' => 'Le contenu original a été restauré.',
    'TOPIC_TRANSLATE_LOADING' => 'Chargement du traducteur…',
    'TOPIC_TRANSLATE_WIDGET_BLOCKED' => 'Le widget de traduction n’a pas été chargé. Vérifiez les blocages du navigateur, la CSP ou les filtres réseau, puis réessayez.',
    'TOPIC_TRANSLATE_WIDGET_CONFLICT' => 'Un autre widget GTranslate est déjà actif sur cette page. La traduction individuelle des messages a été désactivée afin d’éviter un conflit de configuration.',
]);
