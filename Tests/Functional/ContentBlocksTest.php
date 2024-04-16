<?php

declare(strict_types=1);

namespace Codappix\ResponsiveImages\Tests;

/*
 * Copyright (C) 2024 Daniel Gohlke <daniel.gohlke@codappix.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use Codappix\Typo3PhpDatasets\TestingFramework;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class ContentBlocksTest extends FunctionalTestCase
{
    use TestingFramework;

    protected function setUp(): void
    {
        $this->coreExtensionsToLoad = [
        ];

        $this->testExtensionsToLoad = [
            'codappix/typo3-responsive-images',
            'contentblocks/content-blocks',
            'typo3conf/ext/responsive_images/Tests/Fixtures/content_blocks_example',
        ];

        $this->pathsToLinkInTestInstance = [
            'typo3conf/ext/responsive_images/Tests/Fixtures/fileadmin/test_data' => 'fileadmin/test_data',
            'typo3conf/ext/responsive_images/Tests/Fixtures/config/sites' => 'typo3conf/sites',
        ];

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../Fixtures/BaseDatabase.php');
        $this->setUpFrontendRootPage(1, [
            'EXT:responsive_images/Configuration/TypoScript/Setup.typoscript',
            'EXT:content_blocks_example/Configuration/TypoScript/Setup.typoscript',
            'EXT:content_blocks_example/Configuration/TypoScript/Rendering.typoscript',
        ]);
    }

    public static function imageScalingValuesDataProvider(): iterable
    {
        yield '0 Column' => [
            '0colDatabase.php',
            [
                '0' => 'mobile 734 (max-width: 480px)',
                '1' => 'mobile 704 (max-width: 767px)',
                '2' => 'tablet 924 (max-width: 991px)',
                '3' => 'default 1124 (max-width: 1479px)',
                '4' => 'large 562 (min-width: 1480px)',
            ],
        ];
        yield '0 Column with ImageFixWidth' => [
            '0colImageFixWidthDatabase.php',
            [
                '0' => 'mobile 600 (max-width: 480px)',
                '1' => 'mobile 900 (max-width: 767px)',
                '2' => 'tablet 1200 (max-width: 991px)',
                '3' => 'default 1600 (max-width: 1479px)',
                '4' => 'large 1600 (min-width: 1480px)',
            ],
        ];
    }

    #[Test]
    #[DataProvider(methodName: 'imageScalingValuesDataProvider')]
    public function imageIsScaledCorrectly(string $phpDataSet, array $expectedValues): void
    {
        $this->importPHPDataSet(__DIR__ . '/../Fixtures/content_blocks_example/Test/Fixtures/' . $phpDataSet);

        $request = new InternalRequest();
        $request = $request->withPageId(2);

        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());

        foreach ($expectedValues as $expectedValue) {
            self::assertStringContainsString($expectedValue, (string) $result->getBody());
        }
    }
}
