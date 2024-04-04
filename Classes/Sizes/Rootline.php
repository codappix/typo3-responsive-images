<?php

declare(strict_types=1);

namespace Codappix\ResponsiveImages\Sizes;

/*
 * Copyright (C) 2020 Justus Moroni <justus.moroni@codappix.com>
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

use B13\Container\Tca\Registry;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Error\Exception;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageLayoutResolver;

final class Rootline
{
    private readonly ContentElementInterface $contentElement;

    private BackendLayout $backendLayout;

    private array $rootline = [];

    private array $finalSizes = [];

    private string $fieldName;

    public function __construct(array $data, string $fieldName)
    {
        $this->determineBackendLayout();
        $this->fieldName = $fieldName;
        $this->contentElement = $this->determineContentElement($data);

        $this->determineRootline();
        $this->calculateSizes();
    }

    public function getFinalSizes(): array
    {
        return $this->finalSizes;
    }

    private function determineBackendLayout(): void
    {
        $typoscriptFrontendController = $GLOBALS['TSFE'];

        $backendLayoutIdentifier = GeneralUtility::makeInstance(PageLayoutResolver::class)
            ->getLayoutForPage($typoscriptFrontendController->page, $typoscriptFrontendController->rootLine)
        ;

        $this->backendLayout = new BackendLayout($backendLayoutIdentifier);
    }

    private function determineContentElement(array $data): ContentElementInterface
    {
        if (
            class_exists(Registry::class)
            && GeneralUtility::makeInstance(Registry::class)->isContainerElement($data['CType'])
        ) {
            return new Container($data);
        }

        return new ContentElement($data, $this->fieldName);
    }

    private function determineRootline(): void
    {
        $this->rootline[] = $this->contentElement;

        $this->parseRootline($this->contentElement);
    }

    private function parseRootline(ContentElementInterface $contentElement): void
    {
        if (array_key_exists($contentElement->getColPos(), $this->backendLayout->getColumns())) {
            $this->backendLayout->setActiveColumn(
                $this->backendLayout->getColumn($contentElement->getColPos())
            );

            return;
        }

        if (ExtensionManagementUtility::isLoaded('b13/container')) {
            $parentContainer = $contentElement->getData('tx_container_parent');
            assert(is_int($parentContainer));
            $parent = $this->fetchContentElementFromDatabase($parentContainer);

            $this->rootline[] = $parent;
            $this->parseRootline($parent);

            $contentElement->setParent($parent);
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    private function fetchContentElementFromDatabase(int $identifier): ContentElementInterface
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $rawData = $queryBuilder
            ->select('*')
            ->from('tt_content')
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($identifier, Connection::PARAM_INT)))
            ->executeQuery()
            ->fetchAssociative()
        ;

        if ($rawData === false) {
            throw new Exception("Content element '" . $identifier . "' not found.");
        }

        return $this->determineContentElement($rawData);
    }

    private function calculateSizes(): void
    {
        [$sizes, $multiplier] = $this->getSizesAndMultiplierFromRootline();

        $this->calculateFinalSizes($sizes, $multiplier);
    }

    private function getSizesAndMultiplierFromRootline(): array
    {
        $multiplier = [];
        $sizes = [];

        foreach ($this->rootline as $contentElement) {
            if ($contentElement instanceof ContentElementInterface) {
                $sizes = $contentElement->getSizes();
                if (!empty($sizes)) {
                    break;
                }
                $multiplier[] = $contentElement->getMultiplier();
            }
        }

        if (empty($sizes)) {
            $sizes = $this->backendLayout->getSizes();
        }

        return [$sizes, $multiplier];
    }

    private function calculateFinalSizes(array $sizes, array $multiplier): void
    {
        foreach ($sizes as $sizeName => &$size) {
            foreach ($multiplier as $multiplierItem) {
                if (isset($multiplierItem[$sizeName]) === false) {
                    continue;
                }

                $size *= $multiplierItem[$sizeName];
            }

            $size = ceil($size);
        }

        $this->finalSizes = $sizes;
    }
}
