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
    'TOPIC_TRANSLATE_TITLE'    => 'Traduzir este post',
    'TOPIC_TRANSLATE_RESET'    => 'Voltar ao idioma original',
    'TOPIC_TRANSLATE_POWERED'  => 'Traduzido por',
    'TOPIC_TRANSLATE_USE_LAST' => 'Usar último idioma',
    'TRANSLATE'                => 'Traduzir',
    'TOPIC_TRANSLATE_SERVICE_UNAVAILABLE' => 'O serviço de tradução está indisponível no momento. Tente novamente em instantes.',
    'TOPIC_TRANSLATE_RESET_DONE' => 'Conteúdo original restaurado.',
    'TOPIC_TRANSLATE_LOADING' => 'Carregando tradutor…',
    'TOPIC_TRANSLATE_WIDGET_BLOCKED' => 'O widget de tradução não carregou. Verifique bloqueios do navegador, CSP ou filtros de rede e tente novamente.',
]);