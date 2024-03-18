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

use Codappix\ResponsiveImages\Configuration\ConfigurationManager;
use Codappix\ResponsiveImages\Sizes\Breakpoint;
use Codappix\ResponsiveImages\Sizes\Multiplier;
use Codappix\ResponsiveImages\Sizes\Rootline;
use TYPO3\CMS\Core\Error\Exception;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

final class ResponsiveImagesProcessor implements DataProcessorInterface
{
    private readonly ConfigurationManager $configurationManager;

    /**
     * The processor configuration
     */
    private array $processorConfiguration;

    /**
     * @var FileInterface[]
     */
    private array $files = [];

    private array $calculatedFiles = [];

    private array $contentElementData = [];

    private array $contentElementSizes = [];

    private array $contentElementFieldConfiguration = [];

    public function __construct()
    {
        $this->configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
    }

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ) {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }
        $this->processorConfiguration = $processorConfiguration;
        $this->contentElementData = $processedData['data'];

        $filesDataKey = (string) $cObj->stdWrapValue(
            'filesDataKey',
            $processorConfiguration,
            'files'
        );
        if (isset($processedData[$filesDataKey]) && is_array($processedData[$filesDataKey])) {
            $this->files = $processedData[$filesDataKey];
        } else {
            // Files key is empty or not configured.
            return $processedData;
        }

        $this->contentElementSizes = (new Rootline($processedData['data']))->getFinalSizes();
        $this->fetchContentElementFieldConfiguration();
        $this->calculateFileDimensions();

        $targetFieldName = (string) $cObj->stdWrapValue(
            'as',
            $processorConfiguration,
            'responsiveImages'
        );

        $processedData[$targetFieldName] = $this->calculatedFiles;

        return $processedData;
    }

    private function fetchContentElementFieldConfiguration(): void
    {
        $contentElementFieldPath = implode('.', [
            'contentelements',
            $this->contentElementData['CType'],
            $this->processorConfiguration['fieldName'],
        ]);

        if ($this->configurationManager->isValidPath($contentElementFieldPath) === false) {
            throw new Exception("Field configuration '" . $contentElementFieldPath . "' missing.");
        }

        if (is_array($this->configurationManager->getByPath($contentElementFieldPath))) {
            $this->contentElementFieldConfiguration = $this->configurationManager->getByPath($contentElementFieldPath);
        }
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
        $fieldConfiguration = $this->contentElementFieldConfiguration;

        $breakpoints = $this->getBreakpoints();

        /** @var Breakpoint $breakpoint */
        foreach ($breakpoints as $breakpoint) {
            if (isset($this->contentElementSizes[$breakpoint->getIdentifier()]) === false) {
                continue;
            }

            $contentElementSize = $this->contentElementSizes[$breakpoint->getIdentifier()];
            $fileDimensions[$breakpoint->getIdentifier()] = [
                'breakpoint' => $breakpoint,
            ];

            if (isset($fieldConfiguration['multiplier'])) {
                $fileDimensions[$breakpoint->getIdentifier()]['size'] = $contentElementSize
                    * Multiplier::parse(
                        $fieldConfiguration['multiplier'][$breakpoint->getIdentifier()]
                    );

                continue;
            }

            if (isset($fieldConfiguration['sizes'])) {
                $fileDimensions[$breakpoint->getIdentifier()]['size'] = Multiplier::parse(
                    $fieldConfiguration['sizes'][$breakpoint->getIdentifier()]
                );

                continue;
            }
        }

        return $fileDimensions;
    }

    private function getBreakpoints(): array
    {
        $breakpoints = [];

        $breakpointsByPath = $this->configurationManager->getByPath('breakpoints');

        if (is_iterable($breakpointsByPath)) {
            foreach ($breakpointsByPath as $breakpointIdentifier => $breakpointData) {
                $breakpoints[$breakpointIdentifier] = new Breakpoint($breakpointIdentifier, $breakpointData);
            }
        }

        return $breakpoints;
    }
}
