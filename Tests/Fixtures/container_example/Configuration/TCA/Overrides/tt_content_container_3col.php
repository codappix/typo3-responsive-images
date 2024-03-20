<?php

declare(strict_types=1);

use B13\Container\Tca\ContainerConfiguration;
use B13\Container\Tca\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

(static function (string $cType = 'example_container-3col'): void {
    GeneralUtility::makeInstance(Registry::class)->configureContainer(new ContainerConfiguration(
        $cType,
        '3 Column: 33-33-33',
        '(33% / 33% / 33%)',
        [
            [
                [
                    'name' => 'Column 101',
                    'colPos' => 101,
                ],
                [
                    'name' => 'Column 102',
                    'colPos' => 102,
                ],
                [
                    'name' => 'Column 103',
                    'colPos' => 103,
                ],
            ],
        ]
    ));
})();
