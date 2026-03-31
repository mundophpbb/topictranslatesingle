<?php
namespace mundophpbb\topictranslatesingle\migrations;

class v120 extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return $this->config->offsetExists('topictranslatesingle_compatibility_mode');
    }

    public static function depends_on()
    {
        return ['\\mundophpbb\\topictranslatesingle\\migrations\\v110'];
    }

    public function update_data()
    {
        return [
            ['config.add', ['topictranslatesingle_compatibility_mode', 0]],
        ];
    }

    public function revert_data()
    {
        return [
            ['config.remove', ['topictranslatesingle_compatibility_mode']],
        ];
    }
}
