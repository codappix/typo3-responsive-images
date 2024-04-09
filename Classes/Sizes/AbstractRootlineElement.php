<?php

declare(strict_types=1);

namespace Codappix\ResponsiveImages\Sizes;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractRootlineElement
{
    protected ConfigurationManager $configurationManager;

    protected RootlineElementInterface $parent;

    protected ScalingConfiguration $scalingConfiguration;

    public function __construct()
    {
        $this->configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
    }

    public function getParent(): ?RootlineElementInterface
    {
        return $this->parent;
    }

    public function setParent(RootlineElementInterface $rootlineElement): void
    {
        $this->parent = $rootlineElement;
    }

    public function getScalingConfiguration(): ScalingConfiguration
    {
        return $this->scalingConfiguration;
    }

    public function getFinalSize(array $multiplier): array
    {
        if ($this->getScalingConfiguration()->getSizes()) {
            if (empty($multiplier)) {
                return $this->getScalingConfiguration()->getSizes();
            }

            return $this->multiplyArray($this->getScalingConfiguration()->getSizes(), $multiplier);
        }

        if (is_null($this->getParent())) {
            return $this->multiplyArray($this->getScalingConfiguration()->getMultiplier(), $multiplier);
        }

        return $this->getParent()->getFinalSize(
            $this->multiplyArray($this->getScalingConfiguration()->getMultiplier(), $multiplier)
        );
    }

    protected function multiplyArray(array $factor1, array $factor2): array
    {
        if (empty($factor1)) {
            return $factor2;
        }
        if (empty($factor2)) {
            return $factor1;
        }

        foreach ($factor1 as $sizeName => &$size) {
            if (isset($factor2[$sizeName]) === false) {
                continue;
            }

            $factor1[$sizeName] *= $factor2[$sizeName];
        }

        return $factor1;
    }

    protected function readConfigurationByPath(string $configurationPath): ScalingConfiguration
    {
        $configuration = $this->configurationManager->getByPath($configurationPath);
        if (!is_array($configuration)) {
            $configuration = [];
        }

        return new ScalingConfiguration($configuration);
    }
}
