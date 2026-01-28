<?php

namespace mundophpbb\topictranslatesingle\migrations;

class v100 extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return $this->config->offsetExists('topictranslatesingle_default_language');
    }

    static public function depends_on()
    {
        return ['\phpbb\db\migration\data\v31x\v314'];
    }

    public function update_data()
    {
        return [
            ['config.add', ['topictranslatesingle_default_language', 'en']],
            ['config.add', ['topictranslatesingle_languages', serialize(['en', 'pt', 'es', 'fr', 'de', 'it', 'ru', 'ja', 'zh-CN', 'ar', 'nl', 'pl', 'sv'])]],
            ['module.add', [
                'acp',
                'ACP_CAT_DOT_MODS',
                'ACP_TOPICTRANSLATESINGLE_TITLE'
            ]],
            ['module.add', [
                'acp',
                'ACP_TOPICTRANSLATESINGLE_TITLE',
                [
                    'module_basename' => '\mundophpbb\topictranslatesingle\acp\main_module',
                    'modes'            => ['settings'],
                ],
            ]],
        ];
    }
}