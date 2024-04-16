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

use Codappix\ResponsiveImages\Service\ResponsiveImageService;
use RuntimeException;
use TYPO3\CMS\ContentBlocks\DataProcessing\ContentBlockData;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

final class ResponsiveImagesProcessor implements DataProcessorInterface
{
    private array $processedData;

    public function __construct(
        private readonly ResponsiveImageService $responsiveImageService,
        private readonly FileRepository $fileRepository
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

        $this->processedData = $processedData;

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
        $targetFieldName = (string) $cObj->stdWrapValue(
            'as',
            $processorConfiguration,
            'responsiveImages'
        );

        $files = $this->getFiles($filesDataKey, $fieldName);

        if (empty($files)) {
            $processedData[$targetFieldName] = [];

            return $processedData;
        }

        $tsfe = $cObj->getRequest()->getAttribute('frontend.controller');
        if (!$tsfe instanceof TypoScriptFrontendController) {
            throw new RuntimeException('Could not fetch TypoScriptFrontendController from request.', 1712819889);
        }

        if (
            $this->processedData['data'] instanceof ContentBlockData
        ) {
            assert(is_array($this->processedData['data']->_raw));
            $data = $this->processedData['data']->_raw;
        } else {
            $data = $this->processedData['data'];
        }

        $processedData[$targetFieldName] = $this->responsiveImageService->getCalculatedFiles($files, $data, $fieldName, $tsfe);

        return $processedData;
    }

    private function getFiles(string $filesDataKey, string $fieldName): array
    {
        if (
            isset($this->processedData[$filesDataKey])
            && is_array($this->processedData[$filesDataKey])
        ) {
            return $this->processedData[$filesDataKey];
        }

        if ($fieldName === '') {
            return [];
        }

        if ($this->processedData['data'] instanceof ContentBlockData) {
            return $this->processedData['data']->{$fieldName};
        }

        return $this->fileRepository->findByRelation(
            'tt_content',
            $fieldName,
            $this->processedData['data']['uid']
        );
    }
}
