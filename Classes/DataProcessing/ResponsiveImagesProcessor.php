<?php

declare(strict_types=1);

namespace Codappix\ResponsiveImages\DataProcessing;

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

use Codappix\ResponsiveImages\Domain\Factory\BreakpointFactory;
use Codappix\ResponsiveImages\Domain\Factory\RootlineFactory;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

final class ResponsiveImagesProcessor implements DataProcessorInterface
{
    /**
     * @var FileInterface[]
     */
    private array $files = [];

    private array $calculatedFiles = [];

    private array $contentElementSizes = [];

    public function __construct(
        private readonly BreakpointFactory $breakpointFactory,
        private readonly RootlineFactory $rootlineFactory
    ) {
    }

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $filesDataKey = (string) $cObj->stdWrapValue(
            'filesDataKey',
            $processorConfiguration,
            'files'
        );
        $fieldName = (string) $cObj->stdWrapValue(
            'fieldName',
            $processorConfiguration,
            'image'
        );
        if (isset($processedData[$filesDataKey]) && is_array($processedData[$filesDataKey])) {
            $this->files = $processedData[$filesDataKey];
        } else {
            // Files key is empty or not configured.
            return $processedData;
        }

        $rootline = $this->rootlineFactory->create($processedData['data'], $fieldName);
        $this->contentElementSizes = $rootline->getFinalSize();
        $this->calculateFileDimensions();

        $targetFieldName = (string) $cObj->stdWrapValue(
            'as',
            $processorConfiguration,
            'responsiveImages'
        );

        $processedData[$targetFieldName] = $this->calculatedFiles;

        return $processedData;
    }

    private function calculateFileDimensions(): void
    {
        foreach ($this->files as $file) {
            $calculatedFile = [
                'media' => $file,
                'sizes' => $this->calculateFileDimensionForBreakpoints(),
            ];

            $this->calculatedFiles[] = $calculatedFile;
        }
    }

    private function calculateFileDimensionForBreakpoints(): array
    {
        $fileDimensions = [];

        $breakpoints = $this->breakpointFactory->getByConfigurationPath(['breakpoints']);

        foreach ($breakpoints as $breakpoint) {
            if (isset($this->contentElementSizes[$breakpoint->getIdentifier()]) === false) {
                continue;
            }

            $fileDimensions[$breakpoint->getIdentifier()] = [
                'breakpoint' => $breakpoint,
                'size' => $this->contentElementSizes[$breakpoint->getIdentifier()],
            ];
        }

        return $fileDimensions;
    }
}
