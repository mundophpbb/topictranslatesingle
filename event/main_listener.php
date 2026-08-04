<?php
namespace mundophpbb\topictranslatesingle\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use mundophpbb\topictranslatesingle\config\supported_languages;

class main_listener implements EventSubscriberInterface
{
    /** @var \phpbb\config\config */
    protected $config;

    /** @var \phpbb\template\template */
    protected $template;

    /** @var \phpbb\config\db_text */
    protected $config_text;

    public function __construct(
        \phpbb\config\config $config,
        \phpbb\template\template $template,
        \phpbb\config\db_text $config_text
    ) {
        $this->config = $config;
        $this->template = $template;
        $this->config_text = $config_text;
    }

    public static function getSubscribedEvents()
    {
        return [
            'core.viewtopic_assign_template_vars_before' => 'add_gtranslate_vars',
        ];
    }

    public function add_gtranslate_vars($event)
    {
        $forum_id = (int) $event['forum_id'];
        $is_enabled_forum = $this->forum_is_enabled($forum_id);
        $supported_languages = supported_languages::get();
        $language_flags = supported_languages::get_flags();
        $selected_languages = $this->get_selected_languages($supported_languages);
        $board_language = $this->get_default_language($supported_languages);
        $default_language = $this->get_forum_language($forum_id, $supported_languages, $board_language);
        if ($default_language !== $board_language && !in_array($board_language, $selected_languages, true))
        {
            array_unshift($selected_languages, $board_language);
        }
        if (!in_array($default_language, $selected_languages, true))
        {
            array_unshift($selected_languages, $default_language);
        }
        $language_options = [];
        foreach ($selected_languages as $language_code)
        {
            if ($language_code === $default_language)
            {
                continue;
            }

            $language_options[] = [
                'CODE' => $language_code,
                'NAME' => $supported_languages[$language_code],
                'FLAG' => isset($language_flags[$language_code]) ? $language_flags[$language_code] : 'un',
            ];
        }
        $native_names = $this->config->offsetExists('topictranslatesingle_native_names') ? (bool) $this->config['topictranslatesingle_native_names'] : true;
        $detect_browser = $this->config->offsetExists('topictranslatesingle_detect_browser') ? (bool) $this->config['topictranslatesingle_detect_browser'] : false;
        $color_scheme = $this->config->offsetExists('topictranslatesingle_color_scheme') ? (string) $this->config['topictranslatesingle_color_scheme'] : 'auto';
        if (!in_array($color_scheme, ['auto', 'light', 'dark'], true))
        {
            $color_scheme = 'auto';
        }

        $this->template->assign_vars([
            'S_TOPICTRANSLATESINGLE_ACTIVE' => $is_enabled_forum,
            'GTRANSLATE_DEFAULT_LANGUAGE' => $default_language,
            'GTRANSLATE_DEFAULT_LANGUAGE_NAME' => $supported_languages[$default_language],
            'GTRANSLATE_DEFAULT_LANGUAGE_FLAG' => isset($language_flags[$default_language]) ? $language_flags[$default_language] : 'un',
            'GTRANSLATE_LANGUAGES_JSON' => json_encode(array_values($selected_languages)),
            'GTRANSLATE_LANGUAGE_OPTIONS' => $language_options,
            'GTRANSLATE_NATIVE_NAMES' => $native_names,
            'GTRANSLATE_DETECT_BROWSER' => $detect_browser,
            'GTRANSLATE_COLOR_SCHEME' => $color_scheme,
            'GTRANSLATE_LAZY_LOAD' => $this->config->offsetExists('topictranslatesingle_lazy_load') ? (bool) $this->config['topictranslatesingle_lazy_load'] : true,
            'GTRANSLATE_REMEMBER_LANGUAGE' => $this->config->offsetExists('topictranslatesingle_remember_language') ? (bool) $this->config['topictranslatesingle_remember_language'] : true,
            'GTRANSLATE_COOKIE_DOMAIN' => $this->config->offsetExists('cookie_domain') ? (string) $this->config['cookie_domain'] : '',
            'GTRANSLATE_COOKIE_PATH' => $this->config->offsetExists('cookie_path') ? (string) $this->config['cookie_path'] : '/',
        ]);
    }

    protected function get_selected_languages(array $supported_languages)
    {
        $selected_languages = [];

        if ($this->config->offsetExists('topictranslatesingle_languages'))
        {
            $selected_languages = json_decode($this->config['topictranslatesingle_languages'], true);
        }

        if (!is_array($selected_languages))
        {
            $selected_languages = [];
        }

        $selected_languages = array_values(array_unique(array_filter(array_map('strval', $selected_languages), function ($language) use ($supported_languages) {
            return isset($supported_languages[$language]);
        })));

        if (empty($selected_languages))
        {
            $selected_languages = ['en'];
        }

        return $selected_languages;
    }

    protected function get_default_language(array $supported_languages)
    {
        $default_language = $this->config->offsetExists('topictranslatesingle_default_language')
            ? (string) $this->config['topictranslatesingle_default_language']
            : 'en';

        if (!isset($supported_languages[$default_language]))
        {
            $default_language = 'en';
        }

        return $default_language;
    }

    protected function get_forum_language($forum_id, array $supported_languages, $fallback_language)
    {
        if ($forum_id <= 0)
        {
            return $fallback_language;
        }

        $forum_languages = json_decode((string) $this->config_text->get('topictranslatesingle_forum_languages'), true);
        if (!is_array($forum_languages) || !isset($forum_languages[$forum_id]))
        {
            return $fallback_language;
        }

        $forum_language = (string) $forum_languages[$forum_id];

        return isset($supported_languages[$forum_language]) ? $forum_language : $fallback_language;
    }

    protected function forum_is_enabled($forum_id)
    {
        $enabled_forums = $this->get_enabled_forums();

        if (empty($enabled_forums))
        {
            return true;
        }

        if ($forum_id <= 0)
        {
            return false;
        }

        $is_listed = in_array((int) $forum_id, $enabled_forums, true);
        $forum_mode = $this->config->offsetExists('topictranslatesingle_forum_mode')
            ? (string) $this->config['topictranslatesingle_forum_mode']
            : 'include';

        return $forum_mode === 'exclude' ? !$is_listed : $is_listed;
    }

    protected function get_enabled_forums()
    {
        if (!$this->config->offsetExists('topictranslatesingle_enabled_forums'))
        {
            return [];
        }

        return $this->normalise_forum_ids($this->config['topictranslatesingle_enabled_forums']);
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
}
