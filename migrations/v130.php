<?php
namespace mundophpbb\topictranslatesingle\migrations;

class v130 extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return $this->config->offsetExists('topictranslatesingle_lazy_load')
            && $this->config->offsetExists('topictranslatesingle_remember_language')
            && $this->config->offsetExists('topictranslatesingle_forum_mode');
    }

    public static function depends_on()
    {
        return ['\\mundophpbb\\topictranslatesingle\\migrations\\v120'];
    }

    public function update_data()
    {
        return [
            ['config.add', ['topictranslatesingle_lazy_load', 1]],
            ['config.add', ['topictranslatesingle_remember_language', 1]],
            ['config.add', ['topictranslatesingle_forum_mode', 'include']],
            ['config.remove', ['topictranslatesingle_compatibility_mode']],
        ];
    }

    public function revert_data()
    {
        return [
            ['config.add', ['topictranslatesingle_compatibility_mode', 0]],
            ['config.remove', ['topictranslatesingle_forum_mode']],
            ['config.remove', ['topictranslatesingle_remember_language']],
            ['config.remove', ['topictranslatesingle_lazy_load']],
        ];
    }
}
