<?php

namespace mundophpbb\topictranslatesingle\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class language_listener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'core.user_setup' => 'load_language_on_setup',
        ];
    }

    public function load_language_on_setup($event)
    {
        $lang_set_ext = $event['lang_set_ext'];
        $lang_set_ext[] = [
            'ext_name' => 'mundophpbb/topictranslatesingle',
            'lang_set' => 'common',
        ];
        $event['lang_set_ext'] = $lang_set_ext;
    }
}