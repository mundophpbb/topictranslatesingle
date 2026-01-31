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
        // 1. Lê os idiomas selecionados no ACP
        $selected_languages = $this->config->offsetExists('topictranslatesingle_languages')
            ? json_decode($this->config['topictranslatesingle_languages'], true)
            : [];

        // Fallback seguro
        if (!is_array($selected_languages) || empty($selected_languages)) {
            $selected_languages = ['en'];
        }

        // 2. Lê o idioma padrão
        $default_language = $this->config->offsetExists('topictranslatesingle_default_language')
            ? $this->config['topictranslatesingle_default_language']
            : 'en';

        if (!in_array($default_language, $selected_languages)) {
            $default_language = $selected_languages[0] ?? 'en';
        }

        // 3. Lê as novas opções (Native Names e Detect Browser)
        // Usamos (bool) para garantir que o Twig receba um valor booleano puro
        $native_names = $this->config->offsetExists('topictranslatesingle_native_names') 
            ? (bool) $this->config['topictranslatesingle_native_names'] 
            : true;

        $detect_browser = $this->config->offsetExists('topictranslatesingle_detect_browser') 
            ? (bool) $this->config['topictranslatesingle_detect_browser'] 
            : true;

        // Assign das variáveis para o template (front-end)
        $this->template->assign_vars([
            'GTRANSLATE_DEFAULT_LANGUAGE' => $default_language,
            'GTRANSLATE_LANGUAGES_JSON'   => json_encode($selected_languages),
            'GTRANSLATE_NATIVE_NAMES'     => $native_names,
            'GTRANSLATE_DETECT_BROWSER'   => $detect_browser,
        ]);
    }
}