<?php

declare(strict_types=1);

namespace Codappix\ResponsiveImages\Sizes;

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
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageLayoutResolver;

final class Rootline
{
    private readonly ContentElementInterface $contentElement;

    private BackendLayout $backendLayout;

    private ContainerRepository $containerRepository;

    private array $finalSizes = [];

    private string $fieldName;

    private string $backendLayoutIdentifier;

    public function __construct(
        array $data,
        string $fieldName
    ) {
        $this->containerRepository = new ContainerRepository();

        $this->determineBackendLayout();
        $this->fieldName = $fieldName;
        $this->contentElement = $this->determineContentElement(null, $data);

        $this->determineRootline($this->contentElement);

        $this->finalSizes = $this->contentElement->getFinalSize([]);
    }

    public function getFinalSizes(): array
    {
        $sizes = $this->finalSizes;

        foreach ($sizes as $sizeName => &$size) {
            $size = ceil($size);
        }

        return $sizes;
    }

    public function getSizesAndMultiplierFromContentElement(
        ContentElementInterface $contentElement,
        array $multiplier
    ): array {
        $sizes = $contentElement->getScalingConfiguration()->getSizes();
        if (!empty($sizes)) {
            return [$sizes, $multiplier];
        }

        $multiplier[] = $contentElement->getScalingConfiguration()->getMultiplier();

        return [$sizes, $multiplier];
    }

    private function determineBackendLayout(): void
    {
        $typoscriptFrontendController = $GLOBALS['TSFE'];

        $this->backendLayoutIdentifier = GeneralUtility::makeInstance(PageLayoutResolver::class)
            ->getLayoutForPage($typoscriptFrontendController->page, $typoscriptFrontendController->rootLine)
        ;

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
