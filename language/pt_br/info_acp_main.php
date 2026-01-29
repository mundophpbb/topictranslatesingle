<?php
/**
 * Arquivo de tradução para o módulo ACP da extensão Topic Translate Single.
 * Idioma: Português (pt-br).
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
    'ACP_TOPICTRANSLATESINGLE_TITLE'            => 'Tradução de Tópicos',
    'ACP_TOPICTRANSLATESINGLE'                  => 'Configurações',
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE' => 'Idioma Padrão (default_language)',
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE_EXPLAIN' => 'Idioma padrão (Inglês como base).',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES'        => 'Idiomas Disponíveis (languages)',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_EXPLAIN' => 'Selecione vários idiomas para o menu (Ctrl+clique).',
    'ACP_TOPICTRANSLATESINGLE_SETTING_SAVED'    => 'Configurações salvas com sucesso!',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_TIP' => 'Os idiomas mais comuns já vêm pré-selecionados por padrão. A maioria dos fóruns usa menos de 15 idiomas. Idiomas menos comuns/raros estão no final da lista.',
]);