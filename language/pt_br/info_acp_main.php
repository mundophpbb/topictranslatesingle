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
    'ACP_TOPICTRANSLATESINGLE_TITLE'             => 'Tradução de Tópicos',
    'ACP_TOPICTRANSLATESINGLE'                   => 'Configurações',
    
    // Novas chaves Sim/Não
    'ACP_TTS_NATIVE_NAMES'                       => 'Exibir nomes nativos',
    'ACP_TTS_NATIVE_NAMES_EXPLAIN'               => 'Se ativado, os idiomas aparecerão na sua própria escrita (Ex: Español em vez de Espanhol).',
    
    'ACP_TTS_DETECT_BROWSER'                     => 'Auto-detectar idioma do navegador',
    'ACP_TTS_DETECT_BROWSER_EXPLAIN'             => 'Tenta traduzir automaticamente o conteúdo para o idioma principal do visitante ao abrir o tradutor.',

    // Configurações de Idioma
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE'         => 'Idioma Padrão (default_language)',
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE_EXPLAIN' => 'Idioma que o Google usará como base para a tradução.',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES'                => 'Idiomas Disponíveis (languages)',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_EXPLAIN'        => 'Selecione os idiomas que aparecerão no menu de seleção.',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_TIP'            => 'Dica: Segure Ctrl para selecionar múltiplos. Os idiomas mais comuns estão no topo; os menos comuns estão no final da lista.',
    
    'ACP_TTS_COMPATIBILITY_MODE'                 => 'Modo de compatibilidade',
    'ACP_TTS_COMPATIBILITY_MODE_EXPLAIN'         => 'Carrega o widget externo apenas quando o visitante clica em Traduzir. Útil em páginas mais pesadas, extensões de privacidade ou ambientes onde o script externo não deve rodar imediatamente.',

    'ACP_TOPICTRANSLATESINGLE_SETTING_SAVED'            => 'Configurações salvas com sucesso!',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_REQUIRED'       => 'Selecione pelo menos um idioma válido.',
    'ACP_TOPICTRANSLATESINGLE_ENABLED_FORUMS'           => 'IDs dos fóruns habilitados',
    'ACP_TOPICTRANSLATESINGLE_ENABLED_FORUMS_EXPLAIN'   => 'Opcional. Informe IDs de fóruns separados por vírgula para limitar o tradutor apenas a fóruns específicos. Deixe vazio para permitir em todos os tópicos.'
]);