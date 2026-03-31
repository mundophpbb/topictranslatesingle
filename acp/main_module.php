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
        global $language, $template, $request, $config, $cache;

        $language->add_lang('info_acp_main', 'mundophpbb/topictranslatesingle');

        $this->tpl_name = 'acp_topictranslatesingle_body';
        $this->page_title = $language->lang('ACP_TOPICTRANSLATESINGLE_TITLE');

        add_form_key('mundophpbb_topictranslatesingle_settings');

        $supported_languages = supported_languages::get();

        if ($request->is_set_post('submit'))
        {
            if (!check_form_key('mundophpbb_topictranslatesingle_settings'))
            {
                trigger_error('FORM_INVALID', E_USER_WARNING);
            }

            $default_language = (string) $request->variable('default_language', 'en');
            $native_names = (int) $request->variable('native_names', 1);
            $detect_browser = (int) $request->variable('detect_browser', 1);
            $enabled_forums = $this->normalise_forum_ids((string) $request->variable('enabled_forums', '', true));
            $compatibility_mode = (int) $request->variable('compatibility_mode', 0);
            $selected_languages = $this->get_posted_languages($request);
            $selected_languages = $this->normalise_languages($selected_languages, $supported_languages);

            if (empty($selected_languages))
            {
                trigger_error($language->lang('ACP_TOPICTRANSLATESINGLE_LANGUAGES_REQUIRED'), E_USER_WARNING);
            }

            if (!isset($supported_languages[$default_language]) || !in_array($default_language, $selected_languages, true))
            {
                $default_language = $selected_languages[0];
            }

            $config->set('topictranslatesingle_default_language', $default_language);
            $config->set('topictranslatesingle_languages', json_encode($selected_languages));
            $config->set('topictranslatesingle_native_names', $native_names ? 1 : 0);
            $config->set('topictranslatesingle_detect_browser', $detect_browser ? 1 : 0);
            $config->set('topictranslatesingle_enabled_forums', implode(',', $enabled_forums));
            $config->set('topictranslatesingle_compatibility_mode', $compatibility_mode ? 1 : 0);

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
        if (!isset($supported_languages[$default_language]) || !in_array($default_language, $current_languages, true))
        {
            $default_language = $current_languages[0];
        }

        $native_names = $config->offsetExists('topictranslatesingle_native_names') ? (int) $config['topictranslatesingle_native_names'] : 1;
        $detect_browser = $config->offsetExists('topictranslatesingle_detect_browser') ? (int) $config['topictranslatesingle_detect_browser'] : 1;
        $enabled_forums = $config->offsetExists('topictranslatesingle_enabled_forums') ? (string) $config['topictranslatesingle_enabled_forums'] : '';
        $compatibility_mode = $config->offsetExists('topictranslatesingle_compatibility_mode') ? (int) $config['topictranslatesingle_compatibility_mode'] : 0;

        foreach ($supported_languages as $code => $name)
        {
            $template->assign_block_vars('languages', [
                'CODE' => $code,
                'NAME' => $name . ' (' . $code . ')',
                'SELECTED' => in_array($code, $current_languages, true),
                'POPULAR' => isset(supported_languages::get_popular()[$code]),
            ]);
        }

        $template->assign_vars([
            'DEFAULT_LANGUAGE' => $default_language,
            'NATIVE_NAMES' => (bool) $native_names,
            'DETECT_BROWSER' => (bool) $detect_browser,
            'ENABLED_FORUMS' => $enabled_forums,
            'COMPATIBILITY_MODE' => (bool) $compatibility_mode,
            'U_ACTION' => $this->u_action,
        ]);
    }

    protected function get_posted_languages($request)
    {
        $post_data = $request->get_super_global(\phpbb\request\request_interface::POST);

        if (!isset($post_data['languages']))
        {
            return [];
        }

        return is_array($post_data['languages']) ? $post_data['languages'] : [$post_data['languages']];
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
        $forum_ids = preg_split('/[^0-9]+/', (string) $forum_ids);
        $forum_ids = array_map('intval', is_array($forum_ids) ? $forum_ids : []);
        $forum_ids = array_filter($forum_ids, function ($forum_id) {
            return $forum_id > 0;
        });
        $forum_ids = array_values(array_unique($forum_ids));
        sort($forum_ids, SORT_NUMERIC);

        return $forum_ids;
    }
}
