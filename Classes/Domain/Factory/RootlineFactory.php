<?php

declare(strict_types=1);

namespace Codappix\ResponsiveImages\Domain\Factory;

/*
 * Copyright (C) 2024 Justus Moroni <justus.moroni@codappix.com>
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

use B13\Container\Tca\Registry;
use Codappix\ResponsiveImages\Domain\Model\BackendLayoutInterface;
use Codappix\ResponsiveImages\Domain\Model\RootlineElementInterface;
use Codappix\ResponsiveImages\Domain\Repository\ContainerRepository;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageLayoutResolver;

final class RootlineFactory
{
    private BackendLayoutInterface $backendLayout;

    private string $fieldName;

    private string $backendLayoutIdentifier;

    public function __construct(
        private readonly ContainerRepository $containerRepository,
        private readonly PageLayoutResolver $pageLayoutResolver,
        private readonly BackendLayoutFactory $backendLayoutFactory,
        private readonly RootlineElementFactory $rootlineElementFactory
    ) {
    }

    public function getFinalSizes(
        array $data,
        string $fieldName
    ): array {
        $this->determineBackendLayout();

        $this->fieldName = $fieldName;
        $contentElement = $this->determineContentElement(null, $data);

        $this->determineRootline($contentElement);

        $sizes = $contentElement->getFinalSize([]);

        foreach ($sizes as &$size) {
            $size = ceil($size);
        }

        return $sizes;
    }

    private function determineBackendLayout(): void
    {
        $tsfe = $GLOBALS['TSFE'];

        $this->backendLayoutIdentifier = $this->pageLayoutResolver->getLayoutForPage(
            $tsfe->page,
            $tsfe->rootLine
        );

        $this->backendLayout = $this->backendLayoutFactory->create(
            $this->getConfigPathForBackendLayout()
        );
    }

    private function determineRootline(RootlineElementInterface $contentElement): void
    {
        if (in_array($contentElement->getColPos(), $this->backendLayout->getColumns(), true)) {
            $this->detarmineBackendLayout($contentElement);

            return;
        }

        if (ExtensionManagementUtility::isLoaded('b13/container')) {
            $parentContainer = $contentElement->getData('tx_container_parent');
            assert(is_int($parentContainer));

            $parent = $this->determineContentElement(
                $contentElement,
                $this->containerRepository->findByIdentifier($parentContainer)
            );

            $this->determineRootline($parent);
        }
    }

    private function determineContentElement(
        ?RootlineElementInterface $contentElement,
        array $data
    ): RootlineElementInterface {
        if (
            class_exists(Registry::class)
            && GeneralUtility::makeInstance(Registry::class)->isContainerElement($data['CType'])
            && !is_null($contentElement)
        ) {
            return $this->determineContainer($data, $contentElement);
        }

        $newContentElement = $this->rootlineElementFactory->create(
            $data,
            $this->getConfigPathForContentElement($data['CType'])
        );

        if (!is_null($contentElement)) {
            $contentElement->setParent($newContentElement);
        }

        return $newContentElement;
    }

    private function detarmineBackendLayout(RootlineElementInterface $contentElement): void
    {
        $newBackendLayoutColumn = $this->rootlineElementFactory->create(
            [],
            $this->getConfigPathForBackendLayoutColumn($contentElement)
        );

        $newBackendLayoutColumn->setParent($this->backendLayout);
        $contentElement->setParent($newBackendLayoutColumn);
    }

    private function determineContainer(array $data, RootlineElementInterface $contentElement): RootlineElementInterface
    {
        $newContainerColumn = $this->rootlineElementFactory->create(
            $data,
            $this->getConfigPathForContainerColumn($data['CType'], $contentElement)
        );
        $contentElement->setParent($newContainerColumn);

        $newContainer = $this->rootlineElementFactory->create(
            $data,
            $this->getConfigPathForContainer($data['CType'])
        );
        $newContainerColumn->setParent($newContainer);

        return $newContainer;
    }

    private function getConfigPathForContentElement(string $CType): string
    {
        return implode('.', [
            'contentelements',
            $CType,
            $this->fieldName,
        ]);
    }

    private function getConfigPathForContainer(string $CType): string
    {
        return implode('.', [
            'container',
            $CType,
        ]);
    }

    private function getConfigPathForContainerColumn(string $CType, RootlineElementInterface $contentElement): string
    {
        return implode('.', [
            'container',
            $CType,
            'columns',
            (string) $contentElement->getColPos(),
        ]);
    }

    private function getConfigPathForBackendLayout(): string
    {
        return implode('.', [
            'backendlayouts',
            $this->backendLayoutIdentifier,
        ]);
    }

    private function getConfigPathForBackendLayoutColumn(RootlineElementInterface $contentElement): string
    {
        return implode('.', [
            'backendlayouts',
            $this->backendLayoutIdentifier,
            'columns',
            $contentElement->getColPos(),
        ]);
    }
}
