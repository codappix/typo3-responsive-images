<?php

declare(strict_types=1);

namespace Codappix\ResponsiveImages\Service;

use Codappix\ResponsiveImages\Domain\Factory\BreakpointFactory;
use Codappix\ResponsiveImages\Domain\Factory\RootlineFactory;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

final class ResponsiveImageService
{
    private array $files = [];

    private array $contentElementSizes = [];

    public function __construct(
        private readonly BreakpointFactory $breakpointFactory,
        private readonly RootlineFactory $rootlineFactory
    ) {
    }

    public function getCalculatedFiles(
        array $files,
        array $data,
        string $fieldName,
        TypoScriptFrontendController $tsfe
    ): array {
        $this->files = $files;

        $rootline = $this->rootlineFactory->create($data, $fieldName, $tsfe);
        $this->contentElementSizes = $rootline->getFinalSize();

        return $this->calculateFileDimensions();
    }

    private function calculateFileDimensions(): array
    {
        $calculatedFiles = [];

        foreach ($this->files as $file) {
            $calculatedFiles[] = [
                'media' => $file,
                'sizes' => $this->calculateFileDimensionForBreakpoints(),
            ];
        }

        return $calculatedFiles;
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
