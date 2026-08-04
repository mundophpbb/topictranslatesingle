<?php
namespace mundophpbb\topictranslatesingle\migrations;

class v150 extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return false;
    }

    public static function depends_on()
    {
        return ['\\mundophpbb\\topictranslatesingle\\migrations\\v140'];
    }

    public function update_data()
    {
        return [
            ['config_text.add', ['topictranslatesingle_forum_languages', '{}']],
        ];
    }

    public function revert_data()
    {
        return [
            ['config_text.remove', ['topictranslatesingle_forum_languages']],
        ];
    }
}
