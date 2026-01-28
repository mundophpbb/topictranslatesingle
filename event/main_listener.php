<?php

namespace mundophpbb\topictranslatesingle\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{
    protected $config;
    protected $template;

    public function __construct(\phpbb\config\config $config, \phpbb\template\template $template)
    {
        $this->config = $config;
        $this->template = $template;
    }

    static public function getSubscribedEvents()
    {
        return [
            'core.page_footer' => 'add_gtranslate_vars',
        ];
    }

    public function add_gtranslate_vars($event)
    {
        $languages = unserialize($this->config['topictranslatesingle_languages']);

        $this->template->assign_vars([
            'GTRANSLATE_DEFAULT_LANGUAGE' => $this->config['topictranslatesingle_default_language'],
            'GTRANSLATE_LANGUAGES_JSON'   => json_encode($languages),
        ]);
    }
}