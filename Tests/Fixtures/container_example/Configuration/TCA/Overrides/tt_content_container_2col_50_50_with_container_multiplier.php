<?php

declare(strict_types=1);

use B13\Container\Tca\ContainerConfiguration;
use B13\Container\Tca\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

(static function (string $cType = 'example_container-2col-50-50-with-container-multiplier'): void {
    GeneralUtility::makeInstance(Registry::class)->configureContainer(new ContainerConfiguration(
        $cType,
        '2 Column: 50-50',
        '(50% / 50%)',
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
            ],
        ]
    ));
})();
