<?php

declare(strict_types=1);

return [
    'tt_content' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'hidden' => '0',
            'sorting' => '1',
            'CType' => 'example_container-1col',
            'header' => '1col',
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
            'CType' => 'example_container-2col-50-50-with-container-size',
            'header' => '2col in 2col',
            'deleted' => '0',
            'starttime' => '0',
            'endtime' => '0',
            'colPos' => '101',
            'sys_language_uid' => '0',
            'tx_container_parent' => '1',
        ],
        2 => [
            'uid' => '3',
            'pid' => '2',
            'hidden' => '0',
            'sorting' => '1',
            'CType' => 'image',
            'header' => 'image in 2col in 1col',
            'deleted' => '0',
            'starttime' => '0',
            'endtime' => '0',
            'colPos' => '102',
            'sys_language_uid' => '0',
            'image' => '1',
            'tx_container_parent' => '2',
        ],
    ],
    'sys_file_reference' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'uid_local' => '1',
            'uid_foreign' => '3',
            'tablenames' => 'tt_content',
            'fieldname' => 'image',
        ],
    ],
];
