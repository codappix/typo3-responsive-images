<?php

declare(strict_types=1);

namespace Codappix\ResponsiveImages\Domain\Factory;

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

use Codappix\ResponsiveImages\Configuration\ConfigurationManager;
use Codappix\ResponsiveImages\Domain\Model\Rootline;
use Codappix\ResponsiveImages\Domain\Model\RootlineElementInterface;
use Codappix\ResponsiveImages\Domain\Repository\ContainerRepository;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Frontend\Page\PageLayoutResolver;

final class RootlineFactory
{
    public array $columns = [];

    private RootlineElementInterface $backendLayout;

    private RootlineElementInterface $contentElement;

    private string $backendLayoutIdentifier;

    public function __construct(
        private readonly ConfigurationManager $configurationManager,
        private readonly ContainerRepository $containerRepository,
        private readonly PageLayoutResolver $pageLayoutResolver,
        private readonly RootlineElementFactory $rootlineElementFactory
    ) {
    }

    public function create(
        array $data,
        string $fieldName
    ): Rootline {
        $this->createBackendLayoutRootlineElement();
        $this->determineBackendLayoutColumns();

        $this->createContentElementRootlineElement($data, $fieldName);
        $this->determineRootline($this->contentElement);

        return new Rootline($this->contentElement);
    }

    public function createContainerColumnRootlineElement(
        array $data,
        RootlineElementInterface $contentElement
    ): RootlineElementInterface {
        $newContainerColumn = $this->rootlineElementFactory->create(
            $data,
            [
                'container',
                $data['CType'],
                'columns',
                (string) $contentElement->getColPos(),
            ]
        );
        $contentElement->setParent($newContainerColumn);

        return $newContainerColumn;
    }

    public function determineBackendLayoutColumns(): void
    {
        $columns = $this->configurationManager->getByPath(
            [
                'backendlayouts',
                $this->backendLayoutIdentifier,
                'columns',
            ]
        );

        assert(is_array($columns));
        $this->columns = array_map(static fn ($column): int => (int) $column, array_keys($columns));
    }

    private function createBackendLayoutRootlineElement(): void
    {
        $tsfe = $GLOBALS['TSFE'];

        $this->backendLayoutIdentifier = $this->pageLayoutResolver->getLayoutForPage(
            $tsfe->page,
            $tsfe->rootLine
        );

        $this->backendLayout = $this->rootlineElementFactory->create(
            [],
            [
                'backendlayouts',
                $this->backendLayoutIdentifier,
            ]
        );
    }

    private function createBackendLayoutColumnRootlineElement(RootlineElementInterface $contentElement): void
    {
        $newBackendLayoutColumn = $this->rootlineElementFactory->create(
            [],
            [
                'backendlayouts',
                $this->backendLayoutIdentifier,
                'columns',
                (string) $contentElement->getColPos(),
            ]
        );

        $newBackendLayoutColumn->setParent($this->backendLayout);
        $contentElement->setParent($newBackendLayoutColumn);
    }

    private function createContainerRootlineElement(
        array $data,
        RootlineElementInterface $contentElement
    ): RootlineElementInterface {
        $newContainerColumn = $this->createContainerColumnRootlineElement($data, $contentElement);

        $newContainer = $this->rootlineElementFactory->create(
            $data,
            [
                'container',
                $data['CType'],
            ]
        );
        $newContainerColumn->setParent($newContainer);

        return $newContainer;
    }

    private function createContentElementRootlineElement(
        array $data,
        string $fieldName
    ): void {
        $this->contentElement = $this->rootlineElementFactory->create(
            $data,
            [
                'contentelements',
                $data['CType'],
                $fieldName,
            ]
        );
    }

    private function determineRootline(RootlineElementInterface $contentElement): void
    {
        if (in_array($contentElement->getColPos(), $this->columns, true)) {
            $this->createBackendLayoutColumnRootlineElement($contentElement);

            return;
        }

        if (ExtensionManagementUtility::isLoaded('b13/container')) {
            $parentContainer = $contentElement->getData('tx_container_parent');
            assert(is_int($parentContainer));

            $parent = $this->createContainerRootlineElement(
                $this->containerRepository->findByIdentifier($parentContainer),
                $contentElement
            );

            $this->determineRootline($parent);
        }
    }
}
