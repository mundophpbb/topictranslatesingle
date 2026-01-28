<?php

namespace mundophpbb\topictranslatesingle\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class language_listener implements EventSubscriberInterface
{
    protected $language;

    public function __construct(\phpbb\language\language $language)
    {
        $this->language = $language;
    }

    static public function getSubscribedEvents()
    {
        return [
            'core.user_setup' => 'load_language_on_setup',
        ];
    }

    public function load_language_on_setup($event)
    {
        // Carrega apenas no ACP para evitar overhead no frontend
        if (defined('ADMIN_START'))
        {
            $this->language->add_lang('acp_language', 'mundophpbb/topictranslatesingle');
        }
    }
}