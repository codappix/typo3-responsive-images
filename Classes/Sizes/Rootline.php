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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageLayoutResolver;

final class Rootline
{
    private readonly ContentElementInterface $contentElement;

    private BackendLayout $backendLayout;

    private array $rootline = [];

    private array $finalSizes = [];

    public function __construct(array $data)
    {
        $this->determineBackendLayout();
        $this->contentElement = $this->determineContentElement($data);

        $this->determineRootline();
        $this->calculateSizes();
    }

    public static function make(mixed ...$arguments): static
    {
        return new self(...$arguments);
    }

    public function getFinalSizes(): array
    {
        return $this->finalSizes;
    }

    public function getMultiplier(): array
    {
        $multiplier = [
            $this->backendLayout->getActiveColumn()->getMultiplier(),
        ];

        foreach (array_reverse($this->rootline) as $contentElement) {
            if ($contentElement instanceof Container) {
                $multiplier[] = $contentElement->getColumn((string) $this->contentElement->getColPos())->getMultiplier();
            }
        }

        return $multiplier;
    }

    private function determineBackendLayout(): void
    {
        $typoscriptFrontendController = $GLOBALS['TSFE'];

        $backendLayoutIdentifier = GeneralUtility::makeInstance(PageLayoutResolver::class)
            ->getLayoutForPage($typoscriptFrontendController->page, $typoscriptFrontendController->rootLine)
        ;

        $this->backendLayout = BackendLayout::make($backendLayoutIdentifier);
    }

    private function determineContentElement(array $data): ContentElementInterface
    {
        if (str_contains((string) $data['CType'], '_container-')) {
            return Container::make($data);
        }

        return ContentElement::make($data);
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
                $this->backendLayout->getColumn((string) $contentElement->getColPos())
            );

            return;
        }
    }

    private function calculateSizes(): void
    {
        $sizes = $this->backendLayout->getSizes();

        $multiplier = $this->getMultiplier();

        foreach ($sizes as $sizeName => &$size) {
            foreach ($multiplier as $multiplierItem) {
                if (isset($multiplierItem[$sizeName]) === false) {
                    continue;
                }

                $size *= $multiplierItem[$sizeName];
            }
        }

        $this->finalSizes = $sizes;
    }
}
