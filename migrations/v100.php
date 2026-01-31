<?php
namespace mundophpbb\topictranslatesingle\migrations;

class v100 extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return $this->config->offsetExists('topictranslatesingle_default_language');
    }

    public static function depends_on()
    {
        return ['\phpbb\db\migration\data\v31x\v314'];
    }

    public function update_data()
    {
        return [
            // Configurações base
            ['config.add', ['topictranslatesingle_default_language', 'en']],
            ['config.add', ['topictranslatesingle_languages', json_encode(['en', 'pt', 'es', 'fr', 'de', 'it', 'ru', 'ja', 'zh-CN', 'ar', 'hi', 'ko', 'nl', 'pl'])]],
            
            // Novas opções para o ACP
            ['config.add', ['topictranslatesingle_native_names', 1]],
            ['config.add', ['topictranslatesingle_detect_browser', 1]],

            // Categoria principal no ACP (Customise -> Extensões)
            ['module.add', [
                'acp',
                'ACP_CAT_DOT_MODS',
                'ACP_TOPICTRANSLATESINGLE_TITLE'
            ]],

            // Módulo de configurações da extensão
            ['module.add', [
                'acp',
                'ACP_TOPICTRANSLATESINGLE_TITLE',
                [
                    'module_basename' => '\mundophpbb\topictranslatesingle\acp\main_module',
                    'modes'           => ['settings'],
                ],
            ]],
        ];
    }

    public function revert_data()
    {
        return [
            // Remove na ordem inversa: primeiro o modo, depois a categoria
            ['module.remove', [
                'acp',
                'ACP_TOPICTRANSLATESINGLE_TITLE',
                [
                    'module_basename' => '\mundophpbb\topictranslatesingle\acp\main_module',
                    'modes'           => ['settings'],
                ],
            ]],

            ['module.remove', [
                'acp',
                'ACP_CAT_DOT_MODS',
                'ACP_TOPICTRANSLATESINGLE_TITLE'
            ]],

            // Remove todas as configurações da base de dados
            ['config.remove', ['topictranslatesingle_detect_browser']],
            ['config.remove', ['topictranslatesingle_native_names']],
            ['config.remove', ['topictranslatesingle_languages']],
            ['config.remove', ['topictranslatesingle_default_language']],
        ];
    }
}