<?php

namespace mundophpbb\topictranslatesingle\acp;

class main_info
{
    public function module()
    {
        return [
            'filename'  => '\mundophpbb\topictranslatesingle\acp\main_module',
            'title'     => 'ACP_TOPICTRANSLATESINGLE_TITLE',
            'modes'     => [
                'settings' => [
                    'title' => 'ACP_TOPICTRANSLATESINGLE',
                    'auth'  => 'ext_mundophpbb/topictranslatesingle && acl_a_board',
                    'cat'   => ['ACP_TOPICTRANSLATESINGLE_TITLE'],
                ],
            ],
        ];
    }
}