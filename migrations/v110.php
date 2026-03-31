<?php
namespace mundophpbb\topictranslatesingle\migrations;

class v110 extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return $this->config->offsetExists('topictranslatesingle_enabled_forums');
    }

    public static function depends_on()
    {
        return ['\\mundophpbb\\topictranslatesingle\\migrations\\v100'];
    }

    public function update_data()
    {
        return [
            ['config.add', ['topictranslatesingle_enabled_forums', '']],
        ];
    }

    public function revert_data()
    {
        return [
            ['config.remove', ['topictranslatesingle_enabled_forums']],
        ];
    }
}
