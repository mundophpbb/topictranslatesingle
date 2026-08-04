<?php
/**
 * Arquivo de idioma do ACP da extensão Topic Translate Single.
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
    'ACP_TOPICTRANSLATESINGLE_TITLE' => 'Tradução de Tópicos',
    'ACP_TOPICTRANSLATESINGLE' => 'Configurações',
    'ACP_TTS_INTRO' => 'Configure a tradução individual por post. O conteúdo original permanece intacto e, por padrão, o serviço externo só é carregado quando necessário.',

    'ACP_TTS_LANGUAGE_SECTION' => 'Idiomas',
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE' => 'Idioma original do fórum',
    'ACP_TOPICTRANSLATESINGLE_DEFAULT_LANGUAGE_EXPLAIN' => 'Idioma de origem dos posts. Esta opção é independente dos idiomas de destino da tradução.',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES' => 'Idiomas de destino',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_EXPLAIN' => 'Selecione os idiomas que os visitantes poderão escolher no tradutor.',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_TIP' => 'Use a pesquisa e os botões de seleção para gerenciar a lista.',
    'ACP_TOPICTRANSLATESINGLE_LANGUAGES_REQUIRED' => 'Selecione pelo menos um idioma de destino válido.',
    'ACP_TTS_SEARCH_LANGUAGE' => 'Pesquisar idiomas…',
    'ACP_TTS_LANGUAGE_ACTIONS' => 'Ações de seleção de idiomas',
    'ACP_TTS_SELECT_POPULAR' => 'Selecionar populares',
    'ACP_TTS_SELECT_ALL' => 'Selecionar todos',
    'ACP_TTS_SELECT_NONE' => 'Desmarcar todos',
    'ACP_TTS_POPULAR_LANGUAGES' => 'Idiomas populares',
    'ACP_TTS_OTHER_LANGUAGES' => 'Outros idiomas',
    'ACP_TTS_NATIVE_NAMES' => 'Exibir nomes nativos',
    'ACP_TTS_NATIVE_NAMES_EXPLAIN' => 'Exibe os nomes dos idiomas na própria escrita quando houver suporte do widget.',
    'ACP_TTS_DETECT_BROWSER' => 'Detectar idioma do navegador',
    'ACP_TTS_DETECT_BROWSER_EXPLAIN' => 'Permite que o widget sugira o idioma do navegador do visitante. Desativado por padrão para manter o comportamento previsível.',

    'ACP_TTS_APPEARANCE_SECTION' => 'Aparência',
    'ACP_TTS_COLOR_SCHEME' => 'Paleta do tradutor',
    'ACP_TTS_COLOR_SCHEME_EXPLAIN' => 'O modo automático detecta o fundo do fórum e usa a preferência do navegador como alternativa. Também é possível forçar a paleta clara ou escura.',
    'ACP_TTS_COLOR_SCHEME_AUTO' => 'Automática',
    'ACP_TTS_COLOR_SCHEME_LIGHT' => 'Clara',
    'ACP_TTS_COLOR_SCHEME_DARK' => 'Escura',

    'ACP_TTS_FORUM_SECTION' => 'Disponibilidade por fórum',
    'ACP_TTS_FORUM_MODE' => 'Comportamento da lista de fóruns',
    'ACP_TTS_FORUM_MODE_EXPLAIN' => 'Escolha se os fóruns selecionados serão os únicos permitidos ou os fóruns onde a tradução ficará bloqueada.',
    'ACP_TTS_FORUM_MODE_INCLUDE' => 'Ativar somente nos fóruns selecionados',
    'ACP_TTS_FORUM_MODE_EXCLUDE' => 'Ativar em todos, exceto nos selecionados',
    'ACP_TOPICTRANSLATESINGLE_ENABLED_FORUMS' => 'Fóruns',
    'ACP_TOPICTRANSLATESINGLE_ENABLED_FORUMS_EXPLAIN' => 'Selecione os fóruns pelo nome. Sem nenhuma seleção, a tradução permanecerá disponível em todos os fóruns com tópicos.',
    'ACP_TTS_SEARCH_FORUM' => 'Pesquisar fóruns…',
    'ACP_TTS_FORUM_LANGUAGES' => 'Idioma original por fórum',
    'ACP_TTS_FORUM_LANGUAGES_EXPLAIN' => 'Defina outro idioma de origem somente nos fóruns cujo idioma predominante seja diferente da configuração global. Nesses fóruns, o idioma global será oferecido automaticamente como destino da tradução.',
    'ACP_TTS_USE_GLOBAL_LANGUAGE' => 'Usar idioma global: %s',

    'ACP_TTS_PRIVACY_SECTION' => 'Desempenho e privacidade',
    'ACP_TTS_LAZY_LOAD' => 'Carregar somente após o clique',
    'ACP_TTS_LAZY_LOAD_EXPLAIN' => 'Recomendado. O script externo do GTranslate somente será solicitado depois que o visitante abrir o tradutor.',
    'ACP_TTS_REMEMBER_LANGUAGE' => 'Lembrar o último idioma',
    'ACP_TTS_REMEMBER_LANGUAGE_EXPLAIN' => 'Armazena o último idioma escolhido no armazenamento local do navegador do visitante.',
    'ACP_TTS_EXTERNAL_SERVICE_NOTICE' => 'GTranslate e Google Translate são serviços externos. Quando o tradutor é utilizado, o conteúdo da página e dados técnicos da requisição podem ser processados por terceiros. Revise seu aviso de privacidade antes de habilitar o recurso em fóruns privados.',

    'ACP_TOPICTRANSLATESINGLE_SETTING_SAVED' => 'Configurações salvas com sucesso!',
]);
