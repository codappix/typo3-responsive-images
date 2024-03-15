<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Domain\Repository\PageRepository;

return [
    'pages' => [
        0 => [
            'uid' => '1',
            'pid' => '0',
            'title' => 'Root',
            'doktype' => PageRepository::DOKTYPE_DEFAULT,
            'slug' => '/',
            'sorting' => '128',
            'deleted' => '0',
            'backend_layout' => 'pagets__MainTemplate',
            'backend_layout_next_level' => 'pagets__MainTemplate',
        ],
        1 => [
            'uid' => '2',
            'pid' => '1',
            'title' => 'Root',
            'doktype' => PageRepository::DOKTYPE_DEFAULT,
            'slug' => '/test',
            'sorting' => '128',
            'deleted' => '0',
        ],
    ],
    'sys_file_storage' => [
        0 => [
            'uid' => '1',
            'name' => 'test',
            'driver' => 'Local',
            'is_default' => '1',
            'is_public' => '1',
            'is_browsable' => '1',
            'is_writable' => '1',
            'configuration' => '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3FlexForms>
    <data>
        <sheet index="sDEF">
            <language index="lDEF">
                <field index="basePath">
                    <value index="vDEF">fileadmin/</value>
                </field>
                <field index="pathType">
                    <value index="vDEF">relative</value>
                </field>
                <field index="baseUri">
                    <value index="vDEF"></value>
                </field>
                <field index="caseSensitive">
                    <value index="vDEF">1</value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>',
        ],
    ],
    'sys_file' => [
        0 => [
            'uid' => '1',
            'pid' => '0',
            'storage' => '1',
            'type' => '2',
            'identifier' => 'test_data/Example.png',
            'name' => 'Example.png',
            'extension' => 'png',
            'mime_type' => 'image/png',
        ],
    ],
];
