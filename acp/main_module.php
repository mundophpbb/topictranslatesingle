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

        $language->add_lang('info_acp_main', 'mundophpbb/topictranslatesingle');

        $this->tpl_name = 'acp_topictranslatesingle_body';
        $this->page_title = $language->lang('ACP_TOPICTRANSLATESINGLE_TITLE');

        add_form_key('mundophpbb_topictranslatesingle_settings');

        // Idiomas mais populares no topo (os mais usados mundialmente + pt em destaque para BR)
        $popular_languages = [
            'en'  => 'English',
            'pt'  => 'Portuguese',
            'es'  => 'Spanish',
            'fr'  => 'French',
            'de'  => 'German',
            'it'  => 'Italian',
            'ru'  => 'Russian',
            'ja'  => 'Japanese',
            'zh-CN' => 'Chinese (Simplified)',
            'zh-TW' => 'Chinese (Traditional)',
            'ar'  => 'Arabic',
            'hi'  => 'Hindi',
            'ko'  => 'Korean',
            'nl'  => 'Dutch',
            'pl'  => 'Polish',
            'sv'  => 'Swedish',
            'no'  => 'Norwegian',
            'da'  => 'Danish',
            'fi'  => 'Finnish',
            'tr'  => 'Turkish',
            'el'  => 'Greek',
        ];

        // Todos os outros idiomas (ordenados alfabeticamente por nome)
        $other_languages = [
            'af' => 'Afrikaans', 'sq' => 'Albanian', 'am' => 'Amharic', 'hy' => 'Armenian',
            'az' => 'Azerbaijani', 'eu' => 'Basque', 'be' => 'Belarusian', 'bn' => 'Bengali',
            'bs' => 'Bosnian', 'bg' => 'Bulgarian', 'ca' => 'Catalan', 'ceb' => 'Cebuano',
            'ny' => 'Chichewa', 'co' => 'Corsican', 'hr' => 'Croatian', 'cs' => 'Czech',
            'cy' => 'Welsh', 'eo' => 'Esperanto', 'et' => 'Estonian', 'tl' => 'Filipino',
            'fy' => 'Frisian', 'gl' => 'Galician', 'ka' => 'Georgian', 'gu' => 'Gujarati',
            'ht' => 'Haitian Creole', 'ha' => 'Hausa', 'haw' => 'Hawaiian', 'iw' => 'Hebrew',
            'hmn' => 'Hmong', 'hu' => 'Hungarian', 'is' => 'Icelandic', 'ig' => 'Igbo',
            'id' => 'Indonesian', 'ga' => 'Irish', 'jw' => 'Javanese', 'kn' => 'Kannada',
            'kk' => 'Kazakh', 'km' => 'Khmer', 'ku' => 'Kurdish (Kurmanji)', 'ky' => 'Kyrgyz',
            'lo' => 'Lao', 'la' => 'Latin', 'lv' => 'Latvian', 'lt' => 'Lithuanian',
            'lb' => 'Luxembourgish', 'mk' => 'Macedonian', 'mg' => 'Malagasy', 'ms' => 'Malay',
            'ml' => 'Malayalam', 'mt' => 'Maltese', 'mi' => 'Maori', 'mr' => 'Marathi',
            'mn' => 'Mongolian', 'my' => 'Myanmar (Burmese)', 'ne' => 'Neplai', 'fa' => 'Persian',
            'pa' => 'Punjabi', 'ps' => 'Pashto', 'ro' => 'Romanian', 'sm' => 'Samoan',
            'gd' => 'Scottish Gaelic', 'sr' => 'Serbian', 'st' => 'Sesotho', 'sn' => 'Shona',
            'sd' => 'Sindhi', 'si' => 'Sinhala', 'sk' => 'Slovak', 'sl' => 'Slovenian',
            'so' => 'Somali', 'su' => 'Sudanese', 'sw' => 'Swahili', 'tg' => 'Tajik',
            'ta' => 'Tamil', 'te' => 'Telugu', 'th' => 'Thai', 'uk' => 'Ukrainian',
            'ur' => 'Urdu', 'uz' => 'Uzbek', 'vi' => 'Vietnamese', 'xh' => 'Xhosa',
            'yi' => 'Yiddish', 'yo' => 'Yoruba', 'zu' => 'Zulu',
        ];

        // Junta popular primeiro + outros ordenados
        $supported_languages = $popular_languages + $other_languages;

        if ($request->is_set_post('submit'))
        {
            if (!check_form_key('mundophpbb_topictranslatesingle_settings'))
            {
                trigger_error('FORM_INVALID - Chave do formulário inválida. Tente novamente.', E_USER_WARNING);
            }

            $default_lang = $request->variable('default_language', 'en');
            $selected_languages = $request->variable('languages', [], true);

            // Fallback POST cru
            $post_data = $request->get_super_global(\phpbb\request\request_interface::POST);
            if (empty($selected_languages) && isset($post_data['languages'])) {
                $selected_languages = (array) $post_data['languages'];
            }

            $selected_languages = array_unique($selected_languages);

            if (empty($selected_languages)) {
                trigger_error('Nenhum idioma selecionado - por favor, selecione pelo menos um.', E_USER_WARNING);
            }

            // Limite aumentado para 60 (com JSON cabe tranquilo)
            if (count($selected_languages) > 60) {
                trigger_error('Seleção excessiva de idiomas. Limite recomendado: 60. Desmarque alguns menos usados.', E_USER_WARNING);
            }

            $config->set('topictranslatesingle_default_language', $default_lang);
            $config->set('topictranslatesingle_languages', json_encode($selected_languages));

            $cache->purge('config');

            trigger_error($language->lang('ACP_TOPICTRANSLATESINGLE_SETTING_SAVED') . adm_back_link($this->u_action));
        }

        // Leitura das configs
        $current_languages = $config->offsetExists('topictranslatesingle_languages') ? json_decode($config['topictranslatesingle_languages'], true) : [];
        if (!is_array($current_languages)) {
            $current_languages = [];
        }

        $default_language = $config->offsetExists('topictranslatesingle_default_language') ? $config['topictranslatesingle_default_language'] : 'en';

        foreach ($supported_languages as $code => $name)
        {
            $template->assign_block_vars('languages', [
                'CODE'     => $code,
                'NAME'     => $name . ' (' . $code . ')',
                'SELECTED' => in_array($code, $current_languages),
            ]);
        }

        $template->assign_vars([
            'DEFAULT_LANGUAGE' => $default_language,
            'U_ACTION'         => $this->u_action,
        ]);
    }
}