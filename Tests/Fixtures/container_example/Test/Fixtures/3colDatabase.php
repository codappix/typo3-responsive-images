<?php

declare(strict_types=1);

return [
    'tt_content' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'hidden' => '0',
            'sorting' => '1',
            'CType' => 'example_container-3col',
            'header' => '3col',
            'deleted' => '0',
            'starttime' => '0',
            'endtime' => '0',
            'colPos' => '0',
            'sys_language_uid' => '0',
            'tx_container_parent' => '0',
        ],
        1 => [
            'uid' => '2',
            'pid' => '2',
            'hidden' => '0',
            'sorting' => '1',
            'CType' => 'image',
            'header' => 'image in 3col',
            'deleted' => '0',
            'starttime' => '0',
            'endtime' => '0',
            'colPos' => '101',
            'sys_language_uid' => '0',
            'image' => '1',
            'tx_container_parent' => '1',
        ],
    ],
    'sys_file_reference' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'uid_local' => '1',
            'uid_foreign' => '2',
            'tablenames' => 'tt_content',
            'fieldname' => 'image',
        ],
    ],
];
