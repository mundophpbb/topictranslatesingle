<?php
/**
 * Fichier de langue ACP de l’extension Topic Translate Single.
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
    'ACP_TOPICTRANSLATESINGLE_TITLE' => 'Traduction des sujets',
    'ACP_TOPICTRANSLATESINGLE' => 'Paramètres',
    'ACP_TTS_INTRO' => 'Configurez la traduction individuelle des messages. Le message original reste intact et le service externe n’est chargé qu’en cas de besoin par défaut.',

    'ACP_TTS_LANGUAGE_SECTION' => 'Langues',
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE' => 'Langue originale du forum',
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE_EXPLAIN' => 'Langue source des messages, indépendante des langues de destination.',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES' => 'Langues de destination',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_EXPLAIN' => 'Sélectionnez les langues proposées aux visiteurs dans le traducteur.',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_TIP' => 'Utilisez la recherche et les boutons de sélection pour gérer la liste.',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_REQUIRED' => 'Sélectionnez au moins une langue de destination valide.',
    'ACP_TTS_SEARCH_LANGUAGE' => 'Rechercher une langue…',
    'ACP_TTS_LANGUAGE_ACTIONS' => 'Actions de sélection des langues',
    'ACP_TTS_SELECT_POPULAR' => 'Sélectionner les plus utilisées',
    'ACP_TTS_SELECT_ALL' => 'Tout sélectionner',
    'ACP_TTS_SELECT_NONE' => 'Tout désélectionner',
    'ACP_TTS_POPULAR_LANGUAGES' => 'Langues populaires',
    'ACP_TTS_OTHER_LANGUAGES' => 'Autres langues',
    'ACP_TTS_NATIVE_NAMES' => 'Afficher les noms natifs',
    'ACP_TTS_NATIVE_NAMES_EXPLAIN' => 'Affiche les noms des langues dans leur propre écriture lorsque le widget le permet.',
    'ACP_TTS_DETECT_BROWSER' => 'Détecter la langue du navigateur',
    'ACP_TTS_DETECT_BROWSER_EXPLAIN' => 'Permet au widget de suggérer la langue du navigateur. Désactivé par défaut pour un comportement prévisible.',

    'ACP_TTS_APPEARANCE_SECTION' => 'Apparence',
    'ACP_TTS_COLOR_SCHEME' => 'Palette du traducteur',
    'ACP_TTS_COLOR_SCHEME_EXPLAIN' => 'Le mode automatique détecte l’arrière-plan du forum et utilise la préférence du navigateur en dernier recours. Vous pouvez également imposer une palette claire ou sombre.',
    'ACP_TTS_COLOR_SCHEME_AUTO' => 'Automatique',
    'ACP_TTS_COLOR_SCHEME_LIGHT' => 'Claire',
    'ACP_TTS_COLOR_SCHEME_DARK' => 'Sombre',

    'ACP_TTS_FORUM_SECTION' => 'Disponibilité par forum',
    'ACP_TTS_FORUM_MODE' => 'Comportement de la liste',
    'ACP_TTS_FORUM_MODE_EXPLAIN' => 'Choisissez si les forums sélectionnés sont les seuls autorisés ou ceux où la traduction est bloquée.',
    'ACP_TTS_FORUM_MODE_INCLUDE' => 'Activer uniquement dans les forums sélectionnés',
    'ACP_TTS_FORUM_MODE_EXCLUDE' => 'Activer partout sauf dans les forums sélectionnés',
    'ACP_TOPICTRANSLATESINGLE_ENABLED_FORUMS' => 'Forums',
    'ACP_TOPICTRANSLATESINGLE_ENABLED_FORUMS_EXPLAIN' => 'Sélectionnez les forums par leur nom. Sans sélection, la traduction reste disponible dans tous les forums contenant des sujets.',
    'ACP_TTS_SEARCH_FORUM' => 'Rechercher des forums…',
    'ACP_TTS_FORUM_LANGUAGES' => 'Langue originale par forum',
    'ACP_TTS_FORUM_LANGUAGES_EXPLAIN' => 'Définissez une autre langue source uniquement pour les forums dont la langue principale diffère du paramètre global. La langue globale est automatiquement proposée comme destination dans ces forums.',
    'ACP_TTS_USE_GLOBAL_LANGUAGE' => 'Utiliser la langue globale : %s',

    'ACP_TTS_PRIVACY_SECTION' => 'Performances et confidentialité',
    'ACP_TTS_LAZY_LOAD' => 'Charger uniquement après un clic',
    'ACP_TTS_LAZY_LOAD_EXPLAIN' => 'Recommandé. Le script externe GTranslate n’est demandé qu’après l’ouverture du traducteur.',
    'ACP_TTS_REMEMBER_LANGUAGE' => 'Mémoriser la dernière langue',
    'ACP_TTS_REMEMBER_LANGUAGE_EXPLAIN' => 'Enregistre la dernière langue choisie dans le stockage local du navigateur du visiteur.',
    'ACP_TTS_EXTERNAL_SERVICE_NOTICE' => 'GTranslate et Google Translate sont des services externes. Lors de l’utilisation du traducteur, le contenu de la page et des données techniques peuvent être traités par des tiers. Vérifiez votre politique de confidentialité avant d’activer cette fonction dans des forums privés.',

    'ACP_TOPICTRANSLATESINGLE_SETTING_SAVED' => 'Paramètres enregistrés avec succès !',
]);
