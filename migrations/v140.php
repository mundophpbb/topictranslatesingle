<?php
namespace mundophpbb\topictranslatesingle\migrations;

class v140 extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return $this->config->offsetExists('topictranslatesingle_color_scheme');
    }

    public static function depends_on()
    {
        return ['\\mundophpbb\\topictranslatesingle\\migrations\\v130'];
    }

    public function update_data()
    {
        return [
            ['config.add', ['topictranslatesingle_color_scheme', 'auto']],
        ];
    }

    public function revert_data()
    {
        return [
            ['config.remove', ['topictranslatesingle_color_scheme']],
        ];
    }
}
