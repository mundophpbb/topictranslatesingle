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

    /** @var \phpbb\request\request_interface */
    protected $request;

    public function __construct(
        \phpbb\config\config $config,
        \phpbb\template\template $template,
        \phpbb\request\request_interface $request
    ) {
        $this->config = $config;
        $this->template = $template;
        $this->request = $request;
    }

    public static function getSubscribedEvents()
    {
        return [
            'core.page_header_after' => 'add_gtranslate_vars',
            'core.page_footer' => 'add_gtranslate_vars',
        ];
    }

    public function add_gtranslate_vars($event)
    {
        $is_viewtopic = $this->is_viewtopic_page();
        $forum_id = (int) $this->request->variable('f', 0);
        $is_enabled_forum = $this->forum_is_enabled($forum_id);
        $supported_languages = supported_languages::get();
        $selected_languages = $this->get_selected_languages($supported_languages);
        $default_language = $this->get_default_language($selected_languages, $supported_languages);
        $native_names = $this->config->offsetExists('topictranslatesingle_native_names') ? (bool) $this->config['topictranslatesingle_native_names'] : true;
        $detect_browser = $this->config->offsetExists('topictranslatesingle_detect_browser') ? (bool) $this->config['topictranslatesingle_detect_browser'] : true;

        $this->template->assign_vars([
            'S_TOPICTRANSLATESINGLE_ACTIVE' => $is_viewtopic && $is_enabled_forum,
            'GTRANSLATE_DEFAULT_LANGUAGE' => $default_language,
            'GTRANSLATE_LANGUAGES_JSON' => json_encode(array_values($selected_languages)),
            'GTRANSLATE_NATIVE_NAMES' => $native_names,
            'GTRANSLATE_DETECT_BROWSER' => $detect_browser,
            'GTRANSLATE_COMPATIBILITY_MODE' => $this->config->offsetExists('topictranslatesingle_compatibility_mode') ? (bool) $this->config['topictranslatesingle_compatibility_mode'] : false,
        ]);
    }

    protected function is_viewtopic_page()
    {
        $script_name = (string) $this->request->server('SCRIPT_NAME');
        $php_self = (string) $this->request->server('PHP_SELF');
        $php_ext = (string) $this->request->server('PHP_EXT', 'php');
        $topic_id = (int) $this->request->variable('t', 0);

        if ($topic_id > 0)
        {
            return true;
        }

        $candidates = [
            basename($script_name),
            basename($php_self),
        ];

        foreach ($candidates as $candidate)
        {
            if ($candidate === 'viewtopic.' . $php_ext || $candidate === 'app.php')
            {
                return true;
            }
        }

        return false;
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

    protected function get_default_language(array $selected_languages, array $supported_languages)
    {
        $default_language = $this->config->offsetExists('topictranslatesingle_default_language')
            ? (string) $this->config['topictranslatesingle_default_language']
            : 'en';

        if (!isset($supported_languages[$default_language]) || !in_array($default_language, $selected_languages, true))
        {
            $default_language = $selected_languages[0];
        }

        return $default_language;
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
            return true;
        }

        return in_array((int) $forum_id, $enabled_forums, true);
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
