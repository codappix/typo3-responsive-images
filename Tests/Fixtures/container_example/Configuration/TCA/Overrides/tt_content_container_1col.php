<?php

declare(strict_types=1);

use B13\Container\Tca\ContainerConfiguration;
use B13\Container\Tca\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

(static function (string $cType = 'example_container-1col'): void {
    GeneralUtility::makeInstance(Registry::class)->configureContainer(new ContainerConfiguration(
        $cType,
        '1 Column: 100',
        '(100%)',
        [
            [
                [
                    'name' => 'Column 101',
                    'colPos' => 101,
                ],
            ],
        ]
    ));
})();
