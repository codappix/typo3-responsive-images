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
use Codappix\ResponsiveImages\Domain\Repository\ContainerRepository;
use Codappix\ResponsiveImages\Sizes\BackendLayout;
use Codappix\ResponsiveImages\Sizes\BackendLayoutColumn;
use Codappix\ResponsiveImages\Sizes\Container;
use Codappix\ResponsiveImages\Sizes\ContainerColumn;
use Codappix\ResponsiveImages\Sizes\ContentElement;
use Codappix\ResponsiveImages\Sizes\ContentElementInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageLayoutResolver;

final class RootlineFactory
{
    private BackendLayout $backendLayout;

    private string $fieldName;

    private string $backendLayoutIdentifier;

    public function __construct(
        private readonly ContainerRepository $containerRepository,
        private readonly PageLayoutResolver $pageLayoutResolver
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
        $typoscriptFrontendController = $GLOBALS['TSFE'];

        $this->backendLayoutIdentifier = $this->pageLayoutResolver->getLayoutForPage(
            $typoscriptFrontendController->page,
            $typoscriptFrontendController->rootLine
        );

        $this->backendLayout = new BackendLayout($this->backendLayoutIdentifier);
    }

    private function determineContentElement(
        ?ContentElementInterface $contentElement,
        array $data
    ): ContentElementInterface {
        if (
            class_exists(Registry::class)
            && GeneralUtility::makeInstance(Registry::class)->isContainerElement($data['CType'])
            && !is_null($contentElement)
        ) {
            $newContainerColumn = new ContainerColumn($data, $contentElement->getColPos());
            $contentElement->setParent($newContainerColumn);

            $newContainer = new Container($data);
            $newContainerColumn->setParent($newContainer);

            return $newContainer;
        }

        $newContentElement = new ContentElement($data, $this->fieldName);
        if (!is_null($contentElement)) {
            $contentElement->setParent($newContentElement);
        }

        return $newContentElement;
    }

    private function determineRootline(ContentElementInterface $contentElement): void
    {
        if (in_array($contentElement->getColPos(), $this->backendLayout->getColumns(), true)) {
            $newBackendLayoutColumn = new BackendLayoutColumn($this->backendLayoutIdentifier, $contentElement->getColPos());
            $newBackendLayoutColumn->setParent($this->backendLayout);
            $contentElement->setParent($newBackendLayoutColumn);

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
}
