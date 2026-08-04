<?php
namespace mundophpbb\topictranslatesingle\acp;

use mundophpbb\topictranslatesingle\config\supported_languages;

class main_module
{
    public $u_action;
    public $tpl_name;
    public $page_title;

    public function main($id, $mode)
    {
        global $language, $template, $request, $config, $cache, $phpbb_container, $phpbb_root_path, $phpEx;

        $language->add_lang('info_acp_main', 'mundophpbb/topictranslatesingle');

        $this->tpl_name = 'acp_topictranslatesingle_body';
        $this->page_title = $language->lang('ACP_TOPICTRANSLATESINGLE_TITLE');

        add_form_key('mundophpbb_topictranslatesingle_settings');

        $supported_languages = supported_languages::get();
        $config_text = $phpbb_container->get('config_text');

        if ($request->is_set_post('submit'))
        {
            if (!check_form_key('mundophpbb_topictranslatesingle_settings'))
            {
                trigger_error('FORM_INVALID', E_USER_WARNING);
            }

            $default_language = (string) $request->variable('default_language', 'en');
            $native_names = (int) $request->variable('native_names', 1);
            $detect_browser = (int) $request->variable('detect_browser', 0);
            $color_scheme = (string) $request->variable('color_scheme', 'auto');
            $lazy_load = (int) $request->variable('lazy_load', 1);
            $remember_language = (int) $request->variable('remember_language', 1);
            $forum_mode = (string) $request->variable('forum_mode', 'include');
            $enabled_forums = $this->normalise_forum_ids($this->get_posted_values($request, 'enabled_forums'));
            $posted_forum_languages = $this->get_posted_map($request, 'forum_languages');
            $selected_languages = $this->get_posted_languages($request);
            $selected_languages = $this->normalise_languages($selected_languages, $supported_languages);

            if (empty($selected_languages))
            {
                trigger_error($language->lang('ACP_TOPICTRANSLATESINGLE_LANGUAGES_REQUIRED'), E_USER_WARNING);
            }

            if (!isset($supported_languages[$default_language]))
            {
                $default_language = 'en';
            }

            $forum_languages = $this->normalise_forum_languages($posted_forum_languages, $supported_languages, $default_language);

            if (!in_array($forum_mode, ['include', 'exclude'], true))
            {
                $forum_mode = 'include';
            }

            if (!in_array($color_scheme, ['auto', 'light', 'dark'], true))
            {
                $color_scheme = 'auto';
            }

            $config->set('topictranslatesingle_default_language', $default_language);
            $config->set('topictranslatesingle_languages', json_encode($selected_languages));
            $config->set('topictranslatesingle_native_names', $native_names ? 1 : 0);
            $config->set('topictranslatesingle_detect_browser', $detect_browser ? 1 : 0);
            $config->set('topictranslatesingle_color_scheme', $color_scheme);
            $config->set('topictranslatesingle_enabled_forums', implode(',', $enabled_forums));
            $config_text->set('topictranslatesingle_forum_languages', json_encode((object) $forum_languages));
            $config->set('topictranslatesingle_forum_mode', $forum_mode);
            $config->set('topictranslatesingle_lazy_load', $lazy_load ? 1 : 0);
            $config->set('topictranslatesingle_remember_language', $remember_language ? 1 : 0);

            $cache->purge('config');

            trigger_error($language->lang('ACP_TOPICTRANSLATESINGLE_SETTING_SAVED') . adm_back_link($this->u_action));
        }

        $current_languages = [];
        if ($config->offsetExists('topictranslatesingle_languages'))
        {
            $current_languages = json_decode($config['topictranslatesingle_languages'], true);
        }
        if (!is_array($current_languages))
        {
            $current_languages = [];
        }
        $current_languages = $this->normalise_languages($current_languages, $supported_languages);
        if (empty($current_languages))
        {
            $current_languages = ['en'];
        }

        $default_language = $config->offsetExists('topictranslatesingle_default_language') ? (string) $config['topictranslatesingle_default_language'] : 'en';
        if (!isset($supported_languages[$default_language]))
        {
            $default_language = 'en';
        }

        $native_names = $config->offsetExists('topictranslatesingle_native_names') ? (int) $config['topictranslatesingle_native_names'] : 1;
        $detect_browser = $config->offsetExists('topictranslatesingle_detect_browser') ? (int) $config['topictranslatesingle_detect_browser'] : 0;
        $color_scheme = $config->offsetExists('topictranslatesingle_color_scheme') ? (string) $config['topictranslatesingle_color_scheme'] : 'auto';
        if (!in_array($color_scheme, ['auto', 'light', 'dark'], true))
        {
            $color_scheme = 'auto';
        }
        $enabled_forums = $config->offsetExists('topictranslatesingle_enabled_forums') ? (string) $config['topictranslatesingle_enabled_forums'] : '';
        $enabled_forum_ids = $this->normalise_forum_ids($enabled_forums);
        $forum_languages = json_decode((string) $config_text->get('topictranslatesingle_forum_languages'), true);
        $forum_languages = $this->normalise_forum_languages(is_array($forum_languages) ? $forum_languages : [], $supported_languages, $default_language);
        $forum_mode = $config->offsetExists('topictranslatesingle_forum_mode') ? (string) $config['topictranslatesingle_forum_mode'] : 'include';
        $lazy_load = $config->offsetExists('topictranslatesingle_lazy_load') ? (int) $config['topictranslatesingle_lazy_load'] : 1;
        $remember_language = $config->offsetExists('topictranslatesingle_remember_language') ? (int) $config['topictranslatesingle_remember_language'] : 1;
        $popular_languages = supported_languages::get_popular();

        foreach ($supported_languages as $code => $name)
        {
            $block_name = isset($popular_languages[$code]) ? 'popular_languages' : 'other_languages';
            $template->assign_block_vars($block_name, [
                'CODE' => $code,
                'NAME' => $name . ' (' . $code . ')',
                'SELECTED' => in_array($code, $current_languages, true),
            ]);

            $template->assign_block_vars('source_languages', [
                'CODE' => $code,
                'NAME' => $name . ' (' . $code . ')',
                'SELECTED' => $code === $default_language,
            ]);
        }

        if (!function_exists('make_forum_select'))
        {
            include $phpbb_root_path . 'includes/functions_display.' . $phpEx;
        }

        $forum_rows = make_forum_select(false, false, false, false, false, false, true);
        if (is_array($forum_rows))
        {
            foreach ($forum_rows as $forum_row)
            {
                $forum_id = isset($forum_row['forum_id']) ? (int) $forum_row['forum_id'] : 0;
                if ($forum_id <= 0)
                {
                    continue;
                }

                $padding = isset($forum_row['padding'])
                    ? html_entity_decode(strip_tags((string) $forum_row['padding']), ENT_QUOTES | ENT_HTML5, 'UTF-8')
                    : '';

                $template->assign_block_vars('forums', [
                    'ID' => $forum_id,
                    'NAME' => $padding . (string) $forum_row['forum_name'],
                    'SELECTED' => in_array($forum_id, $enabled_forum_ids, true),
                    'DISABLED' => !empty($forum_row['disabled']),
                    'GLOBAL_LANGUAGE_LABEL' => $language->lang(
                        'ACP_TTS_USE_GLOBAL_LANGUAGE',
                        $supported_languages[$default_language] . ' (' . $default_language . ')'
                    ),
                ]);

                foreach ($supported_languages as $language_code => $language_name)
                {
                    $template->assign_block_vars('forums.source_languages', [
                        'CODE' => $language_code,
                        'NAME' => $language_name . ' (' . $language_code . ')',
                        'SELECTED' => isset($forum_languages[$forum_id]) && $forum_languages[$forum_id] === $language_code,
                    ]);
                }
            }
        }

        $template->assign_vars([
            'DEFAULT_LANGUAGE' => $default_language,
            'NATIVE_NAMES' => (bool) $native_names,
            'DETECT_BROWSER' => (bool) $detect_browser,
            'COLOR_SCHEME' => $color_scheme,
            'FORUM_MODE' => $forum_mode,
            'LAZY_LOAD' => (bool) $lazy_load,
            'REMEMBER_LANGUAGE' => (bool) $remember_language,
            'LANGUAGE_COUNT' => count($current_languages),
            'FORUM_COUNT' => count($enabled_forum_ids),
            'FORUM_LANGUAGE_COUNT' => count($forum_languages),
            'U_ACTION' => $this->u_action,
        ]);
    }

    protected function get_posted_languages($request)
    {
        return $this->get_posted_values($request, 'languages');
    }

    protected function get_posted_values($request, $key)
    {
        $post_data = $request->get_super_global(\phpbb\request\request_interface::POST);

        if (!isset($post_data[$key]))
        {
            return [];
        }

        return is_array($post_data[$key]) ? $post_data[$key] : [$post_data[$key]];
    }

    protected function get_posted_map($request, $key)
    {
        $post_data = $request->get_super_global(\phpbb\request\request_interface::POST);

        return isset($post_data[$key]) && is_array($post_data[$key]) ? $post_data[$key] : [];
    }

    protected function normalise_languages(array $languages, array $supported_languages)
    {
        $languages = array_values(array_unique(array_map('strval', $languages)));

        return array_values(array_filter($languages, function ($language) use ($supported_languages) {
            return isset($supported_languages[$language]);
        }));
    }

    protected function normalise_forum_ids($forum_ids)
    {
        if (is_array($forum_ids))
        {
            $forum_ids = implode(',', $forum_ids);
        }

        $forum_ids = preg_split('/[^0-9]+/', (string) $forum_ids);
        $forum_ids = array_map('intval', is_array($forum_ids) ? $forum_ids : []);
        $forum_ids = array_filter($forum_ids, function ($forum_id) {
            return $forum_id > 0;
        });
        $forum_ids = array_values(array_unique($forum_ids));
        sort($forum_ids, SORT_NUMERIC);

        return $forum_ids;
    }

    protected function normalise_forum_languages(array $forum_languages, array $supported_languages, $default_language)
    {
        $normalised = [];

        foreach ($forum_languages as $forum_id => $language_code)
        {
            $forum_id = (int) $forum_id;
            $language_code = (string) $language_code;

            if ($forum_id > 0 && $language_code !== $default_language && isset($supported_languages[$language_code]))
            {
                $normalised[$forum_id] = $language_code;
            }
        }

        ksort($normalised, SORT_NUMERIC);

        return $normalised;
    }
}
