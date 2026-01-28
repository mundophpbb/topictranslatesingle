<?php
namespace mundophpbb\topictranslatesingle\acp;
class main_module
{
    public $u_action;
    public $tpl_name;
    public $page_title;
    public function main($id, $mode)
    {
        global $language, $template, $request, $config, $cache;

        // Carregue o arquivo de linguagem aqui (ajustado para o nome real do arquivo: 'info_acp_main')
        $language->add_lang('info_acp_main', 'mundophpbb/topictranslatesingle');

        $this->tpl_name = 'acp_topictranslatesingle_body';
        $this->page_title = $language->lang('ACP_TOPICTRANSLATESINGLE_TITLE');
        add_form_key('mundophpbb_topictranslatesingle_settings');
        // Log temporário do POST completo ao carregar a página (para depurar envio)
        error_log('TopicTranslateSingle - POST completo ao carregar: ' . print_r($request->get_super_global(\phpbb\request\request_interface::POST), true));
        // Lista de idiomas suportados pelo GTranslate (códigos e nomes)
        $supported_languages = [
            'en' => 'English', 'ar' => 'Arabic', 'bg' => 'Bulgarian', 'zh-CN' => 'Chinese (Simplified)', 'zh-TW' => 'Chinese (Traditional)',
            'hr' => 'Croatian', 'cs' => 'Czech', 'da' => 'Danish', 'nl' => 'Dutch', 'fi' => 'Finnish', 'fr' => 'French', 'de' => 'German',
            'el' => 'Greek', 'hi' => 'Hindi', 'it' => 'Italian', 'ja' => 'Japanese', 'ko' => 'Korean', 'no' => 'Norwegian', 'pl' => 'Polish',
            'pt' => 'Portuguese', 'ro' => 'Romanian', 'ru' => 'Russian', 'es' => 'Spanish', 'sv' => 'Swedish', 'ca' => 'Catalan', 'tl' => 'Filipino',
            'iw' => 'Hebrew', 'id' => 'Indonesian', 'lv' => 'Latvian', 'lt' => 'Lithuanian', 'sr' => 'Serbian', 'sk' => 'Slovak', 'sl' => 'Slovenian',
            'uk' => 'Ukrainian', 'vi' => 'Vietnamese', 'sq' => 'Albanian', 'et' => 'Estonian', 'gl' => 'Galician', 'hu' => 'Hungarian', 'mt' => 'Maltese',
            'th' => 'Thai', 'tr' => 'Turkish', 'fa' => 'Persian', 'af' => 'Afrikaans', 'ms' => 'Malay', 'sw' => 'Swahili', 'ga' => 'Irish', 'cy' => 'Welsh',
            'be' => 'Belarusian', 'is' => 'Icelandic', 'mk' => 'Macedonian', 'yi' => 'Yiddish', 'hy' => 'Armenian', 'az' => 'Azerbaijani', 'eu' => 'Basque',
            'ka' => 'Georgian', 'ht' => 'Haitian Creole', 'ur' => 'Urdu', 'bn' => 'Bengali', 'bs' => 'Bosnian', 'ceb' => 'Cebuano', 'eo' => 'Esperanto',
            'gu' => 'Gujarati', 'ha' => 'Hausa', 'hmn' => 'Hmong', 'ig' => 'Igbo', 'jw' => 'Javanese', 'kn' => 'Kannada', 'km' => 'Khmer', 'lo' => 'Lao',
            'la' => 'Latin', 'mi' => 'Maori', 'mr' => 'Marathi', 'mn' => 'Mongolian', 'ne' => 'Nepali', 'pa' => 'Punjabi', 'so' => 'Somali', 'ta' => 'Tamil',
            'te' => 'Telugu', 'yo' => 'Yoruba', 'zu' => 'Zulu', 'my' => 'Myanmar (Burmese)', 'ny' => 'Chichewa', 'kk' => 'Kazakh', 'mg' => 'Malagasy',
            'ml' => 'Malayalam', 'si' => 'Sinhala', 'st' => 'Sesotho', 'su' => 'Sudanese', 'tg' => 'Tajik', 'uz' => 'Uzbek', 'am' => 'Amharic',
            'co' => 'Corsican', 'haw' => 'Hawaiian', 'ku' => 'Kurdish (Kurmanji)', 'ky' => 'Kyrgyz', 'lb' => 'Luxembourgish', 'ps' => 'Pashto',
            'sm' => 'Samoan', 'gd' => 'Scottish Gaelic', 'sn' => 'Shona', 'sd' => 'Sindhi', 'fy' => 'Frisian', 'xh' => 'Xhosa',
        ];
        if ($request->is_set_post('submit'))
        {
            if (!check_form_key('mundophpbb_topictranslatesingle_settings'))
            {
                trigger_error('FORM_INVALID - Chave do formulário inválida. Tente novamente.', E_USER_WARNING);
            }
            $default_lang = $request->variable('default_language', 'en');
            $selected_languages = $request->variable('languages', [], true); // Array de códigos selecionados
            // Fallback para POST cru se variable retornar vazio
            $post_data = $request->get_super_global(\phpbb\request\request_interface::POST);
            if (empty($selected_languages) && isset($post_data['languages'])) {
                $selected_languages = (array) $post_data['languages']; // Force array
                error_log('TopicTranslateSingle - Usando fallback POST cru para languages: ' . print_r($selected_languages, true));
            }
            // Log temporário para depurar
            error_log('TopicTranslateSingle - Submit recebido. Default Lang: ' . $default_lang);
            error_log('TopicTranslateSingle - Selected Languages: ' . print_r($selected_languages, true));
            if (empty($selected_languages)) {
                trigger_error('Nenhum idioma selecionado - por favor, selecione pelo menos um.', E_USER_WARNING);
            }
            $config->set('topictranslatesingle_default_language', $default_lang);
            $config->set('topictranslatesingle_languages', serialize($selected_languages));
            // Força limpeza de cache das configs
            $cache->purge('config');
            trigger_error($language->lang('ACP_TOPICTRANSLATESINGLE_SETTING_SAVED') . adm_back_link($this->u_action));
        }
        // Atribui vars ao template
        $current_languages = $config->offsetExists('topictranslatesingle_languages') ? unserialize($config['topictranslatesingle_languages']) : [];
        $default_language = $config->offsetExists('topictranslatesingle_default_language') ? $config['topictranslatesingle_default_language'] : 'en';
        foreach ($supported_languages as $code => $name)
        {
            $template->assign_block_vars('languages', [
                'CODE' => $code,
                'NAME' => $name . ' (' . $code . ')',
                'SELECTED' => in_array($code, $current_languages) ? true : false,
            ]);
        }
        $template->assign_vars([
            'DEFAULT_LANGUAGE' => $default_language,
            'U_ACTION' => $this->u_action,
        ]);
    }
}