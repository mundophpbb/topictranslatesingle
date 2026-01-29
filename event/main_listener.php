<?php
namespace mundophpbb\topictranslatesingle\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{
    protected $config;
    protected $template;

    public function __construct(\phpbb\config\config $config, \phpbb\template\template $template)
    {
        $this->config   = $config;
        $this->template = $template;
    }

    public static function getSubscribedEvents()
    {
        return [
            'core.page_footer' => 'add_gtranslate_vars',
        ];
    }

    public function add_gtranslate_vars($event)
    {
        // Lê os idiomas selecionados no ACP (agora armazenado como JSON)
        $selected_languages = $this->config->offsetExists('topictranslatesingle_languages')
            ? json_decode($this->config['topictranslatesingle_languages'], true)
            : [];

        // Fallback seguro caso algo dê errado (ex.: config corrompida)
        if (!is_array($selected_languages) || empty($selected_languages)) {
            $selected_languages = ['en']; // mínimo funcional
        }

        $default_language = $this->config->offsetExists('topictranslatesingle_default_language')
            ? $this->config['topictranslatesingle_default_language']
            : 'en';

        // Garante que o idioma padrão esteja na lista (evita erro no widget GTranslate)
        if (!in_array($default_language, $selected_languages)) {
            $default_language = $selected_languages[0] ?? 'en';
        }

        // Assign das variáveis para o template
        $this->template->assign_vars([
            'GTRANSLATE_DEFAULT_LANGUAGE' => $default_language,
            'GTRANSLATE_LANGUAGES_JSON'   => json_encode($selected_languages),
        ]);
    }
}