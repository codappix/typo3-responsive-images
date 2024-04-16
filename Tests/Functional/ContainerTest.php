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

class ContainerTest extends FunctionalTestCase
{
    use TestingFramework;

    protected function setUp(): void
    {
        $this->coreExtensionsToLoad = [
            'fluid_styled_content',
        ];

        $this->testExtensionsToLoad = [
            'b13/container',
            'codappix/typo3-responsive-images',
            'contentblocks/content-blocks',
            'typo3conf/ext/responsive_images/Tests/Fixtures/base_example',
            'typo3conf/ext/responsive_images/Tests/Fixtures/container_example',
            'typo3conf/ext/responsive_images/Tests/Fixtures/content_blocks_example',
        ];

        $this->pathsToLinkInTestInstance = [
            'typo3conf/ext/responsive_images/Tests/Fixtures/fileadmin/test_data' => 'fileadmin/test_data',
            'typo3conf/ext/responsive_images/Tests/Fixtures/config/sites' => 'typo3conf/sites',
        ];

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../Fixtures/BaseDatabase.php');
        $this->setUpFrontendRootPage(1, [
            'EXT:fluid_styled_content/Configuration/TypoScript/setup.typoscript',
            'EXT:responsive_images/Configuration/TypoScript/Setup.typoscript',
            'EXT:base_example/Configuration/TypoScript/Setup.typoscript',
            'EXT:base_example/Configuration/TypoScript/Rendering.typoscript',
            'EXT:container_example/Configuration/TypoScript/Setup.typoscript',
            'EXT:content_blocks_example/Configuration/TypoScript/ContentElements/codappix_image.typoscript',
            'EXT:content_blocks_example/Configuration/TypoScript/ContentElements/codappix_imagefixedwidth.typoscript',
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
        yield '1 Column' => [
            '1colDatabase.php',
            [
                '0' => 'mobile 734 (max-width: 480px)',
                '1' => 'mobile 704 (max-width: 767px)',
                '2' => 'tablet 924 (max-width: 991px)',
                '3' => 'default 1124 (max-width: 1479px)',
                '4' => 'large 562 (min-width: 1480px)',
            ],
        ];
        yield '2 Column 50-50' => [
            '2col_50_50_Database.php',
            [
                '0' => 'mobile 734 (max-width: 480px)',
                '1' => 'mobile 704 (max-width: 767px)',
                '2' => 'tablet 462 (max-width: 991px)',
                '3' => 'default 562 (max-width: 1479px)',
                '4' => 'large 281 (min-width: 1480px)',
            ],
        ];
        yield '2 Column 66-33 Image in left Column' => [
            '2col_66_33_ImageLeft_Database.php',
            [
                '0' => 'mobile 734 (max-width: 480px)',
                '1' => 'mobile 704 (max-width: 767px)',
                '2' => 'tablet 616 (max-width: 991px)',
                '3' => 'default 749 (max-width: 1479px)',
                '4' => 'large 375 (min-width: 1480px)',
            ],
        ];
        yield '2 Column 66-33 Image in right Column' => [
            '2col_66_33_ImageRight_Database.php',
            [
                '0' => 'mobile 734 (max-width: 480px)',
                '1' => 'mobile 704 (max-width: 767px)',
                '2' => 'tablet 308 (max-width: 991px)',
                '3' => 'default 375 (max-width: 1479px)',
                '4' => 'large 188 (min-width: 1480px)',
            ],
        ];
        yield '2 Column in 1 Column' => [
            '1col2colDatabase.php',
            [
                '0' => 'mobile 734 (max-width: 480px)',
                '1' => 'mobile 704 (max-width: 767px)',
                '2' => 'tablet 462 (max-width: 991px)',
                '3' => 'default 562 (max-width: 1479px)',
                '4' => 'large 281 (min-width: 1480px)',
            ],
        ];
        yield '2 Column in 2 Column' => [
            '2col2colDatabase.php',
            [
                '0' => 'mobile 734 (max-width: 480px)',
                '1' => 'mobile 704 (max-width: 767px)',
                '2' => 'tablet 231 (max-width: 991px)',
                '3' => 'default 281 (max-width: 1479px)',
                '4' => 'large 141 (min-width: 1480px)',
            ],
        ];
        yield '3 Column' => [
            '3colDatabase.php',
            [
                '0' => 'mobile 734 (max-width: 480px)',
                '1' => 'mobile 704 (max-width: 767px)',
                '2' => 'tablet 308 (max-width: 991px)',
                '3' => 'default 375 (max-width: 1479px)',
                '4' => 'large 188 (min-width: 1480px)',
            ],
        ];
        yield '1 Column Full Width' => [
            '1colFullWidthDatabase.php',
            [
                '0' => 'mobile 1254 (max-width: 480px)',
                '1' => 'mobile 1203 (max-width: 767px)',
                '2' => 'tablet 1578 (max-width: 991px)',
                '3' => 'default 1920 (max-width: 1479px)',
                '4' => 'large 960 (min-width: 1480px)',
            ],
        ];
        yield '2 Column in 1 Column Full Width' => [
            '1col2colFullWidthDatabase.php',
            [
                '0' => 'mobile 1254 (max-width: 480px)',
                '1' => 'mobile 1203 (max-width: 767px)',
                '2' => 'tablet 789 (max-width: 991px)',
                '3' => 'default 960 (max-width: 1479px)',
                '4' => 'large 480 (min-width: 1480px)',
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
        yield '3 Column with ImageFixWidth' => [
            '3colImageFixWidthDatabase.php',
            [
                '0' => 'mobile 600 (max-width: 480px)',
                '1' => 'mobile 900 (max-width: 767px)',
                '2' => 'tablet 1200 (max-width: 991px)',
                '3' => 'default 1600 (max-width: 1479px)',
                '4' => 'large 1600 (min-width: 1480px)',
            ],
        ];
        yield '2 Column with Container Size in 1 Column' => [
            '1col2colWidthContainerSizeDatabase.php',
            [
                '0' => 'mobile 600 (max-width: 480px)',
                '1' => 'mobile 900 (max-width: 767px)',
                '2' => 'tablet 600 (max-width: 991px)',
                '3' => 'default 750 (max-width: 1479px)',
                '4' => 'large 450 (min-width: 1480px)',
            ],
        ];
        yield '2 Column with Container Multiplier in 1 Column' => [
            '1col2colWidthContainerMultiplierDatabase.php',
            [
                '0' => 'mobile 588 (max-width: 480px)',
                '1' => 'mobile 564 (max-width: 767px)',
                '2' => 'tablet 370 (max-width: 991px)',
                '3' => 'default 450 (max-width: 1479px)',
                '4' => 'large 225 (min-width: 1480px)',
            ],
        ];
    }

    #[Test]
    #[DataProvider(methodName: 'imageScalingValuesDataProvider')]
    public function imageIsScaledCorrectly(string $phpDataSet, array $expectedValues): void
    {
        $this->importPHPDataSet(__DIR__ . '/../Fixtures/container_example/Test/Fixtures/Content/' . $phpDataSet);

        $request = new InternalRequest();
        $request = $request->withPageId(2);

        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());

        foreach ($expectedValues as $expectedValue) {
            self::assertStringContainsString($expectedValue, (string) $result->getBody());
        }
    }

    #[Test]
    #[DataProvider(methodName: 'imageScalingValuesDataProvider')]
    public function contentBlocksImageIsScaledCorrectly(string $phpDataSet, array $expectedValues): void
    {
        $this->importPHPDataSet(__DIR__ . '/../Fixtures/container_example/Test/Fixtures/ContentBlocks/' . $phpDataSet);

        $request = new InternalRequest();
        $request = $request->withPageId(2);

        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());

        foreach ($expectedValues as $expectedValue) {
            self::assertStringContainsString($expectedValue, (string) $result->getBody());
        }
    }
}
