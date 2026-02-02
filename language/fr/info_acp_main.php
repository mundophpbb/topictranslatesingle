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
    'ACP_TOPICTRANSLATESINGLE_TITLE'                    => 'Topic Translate Single',
    'ACP_TOPICTRANSLATESINGLE'                          => 'Paramètres',

    // Toggle keys
    'ACP_TTS_NATIVE_NAMES'                       => 'Afficher les noms natifs',
    'ACP_TTS_NATIVE_NAMES_EXPLAIN'               => 'Permet d’afficher les langues dans leur script original (par exemple, Español, Français, 日 本 語), ce qui facilite la navigation pour les utilisateurs internationaux.',
    
    'ACP_TTS_DETECT_BROWSER'                     => 'Détection automatique de la langue du navigateur',
    'ACP_TTS_DETECT_BROWSER_EXPLAIN'             => 'Essaie de traduire automatiquement le contenu dans la langue principale du visiteur lors de l’ouverture du traducteur.',

    // Language Settings
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE'         => 'Langue par défaut',
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE_EXPLAIN' => 'Langue par défaut pour la traduction (l’anglais est utilisé comme langue de base).',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES'                => 'Langues disponibles',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_EXPLAIN'        => 'Sélectionnez les langues à afficher dans le menu du traducteur (maintenez Ctrl/Cmd pour sélectionner plusieurs).',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_TIP'            => 'Les langues les plus courantes sont présélectionnées par défaut. La plupart des forums utilisent moins de 15 langues. Les langues moins courantes ou rares sont disponibles en fin de liste.',

    'ACP_TOPICTRANSLATESINGLE_SETTING_SAVED'            => 'Les paramètres ont été enregistrés avec succès !',
]);