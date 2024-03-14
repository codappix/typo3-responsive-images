<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

(static function ($extensionKey, $tableName): void {
    ExtensionManagementUtility::addStaticFile(
        $extensionKey,
        'Configuration/TypoScript',
        'TYPO3 Responsive Images'
    );
})('responsive_images', 'sys_template');
