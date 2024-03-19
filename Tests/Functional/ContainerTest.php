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
            'codappix/responsive-images',
            'typo3conf/ext/responsive_images/Tests/Fixtures/container_example',
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
            'EXT:container_example/Configuration/TypoScript/Setup.typoscript',
            'EXT:container_example/Configuration/TypoScript/Rendering.typoscript',
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
                '4' => 'large 1124 (min-width: 1480px)',
            ],
        ];
        yield '1 Column' => [
            '1colDatabase.php',
            [
                '0' => 'mobile 734 (max-width: 480px)',
                '1' => 'mobile 704 (max-width: 767px)',
                '2' => 'tablet 924 (max-width: 991px)',
                '3' => 'default 1124 (max-width: 1479px)',
                '4' => 'large 1124 (min-width: 1480px)',
            ],
        ];
        yield '2 Column 50-50' => [
            '2col_50_50_Database.php',
            [
                '0' => 'mobile 734 (max-width: 480px)',
                '1' => 'mobile 704 (max-width: 767px)',
                '2' => 'tablet 462 (max-width: 991px)',
                '3' => 'default 562 (max-width: 1479px)',
                '4' => 'large 562 (min-width: 1480px)',
            ],
        ];
        yield '2 Column 66-33' => [
            '2col_66_33_Database.php',
            [
                '0' => 'mobile 734 (max-width: 480px)',
                '1' => 'mobile 704 (max-width: 767px)',
                '2' => 'tablet 615.384 (max-width: 991px)',
                '3' => 'default 748.584 (max-width: 1479px)',
                '4' => 'large 748.584 (min-width: 1480px)',
            ],
        ];
        yield '2 Column in 2 Column' => [
            '2col2colDatabase.php',
            [
                '0' => 'mobile 734 (max-width: 480px)',
                '1' => 'mobile 704 (max-width: 767px)',
                '2' => 'tablet 231 (max-width: 991px)',
                '3' => 'default 281 (max-width: 1479px)',
                '4' => 'large 281 (min-width: 1480px)',
            ],
        ];
        yield '3 Column' => [
            '3colDatabase.php',
            [
                '0' => 'mobile 734 (max-width: 480px)',
                '1' => 'mobile 704 (max-width: 767px)',
                '2' => 'tablet 307.692 (max-width: 991px)',
                '3' => 'default 374.292 (max-width: 1479px)',
                '4' => 'large 374.292 (min-width: 1480px)',
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider imageScalingValuesDataProvider
     */
    public function imageIsScaledCorrectly(string $phpDataSet, array $expectedValues): void
    {
        $this->importPHPDataSet(__DIR__ . '/../Fixtures/container_example/Test/Fixtures/' . $phpDataSet);

        $request = new InternalRequest();
        $request = $request->withPageId(2);

        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());

        foreach ($expectedValues as $expectedValue) {
            self::assertStringContainsString($expectedValue, (string) $result->getBody());
        }
    }
}
