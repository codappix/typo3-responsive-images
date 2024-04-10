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
use Codappix\ResponsiveImages\Domain\Model\BackendLayout;
use Codappix\ResponsiveImages\Domain\Model\BackendLayoutInterface;

class BackendLayoutFactory
{
    public function __construct(
        private readonly ConfigurationManager $configurationManager,
        private readonly ScalingFactory $scalingFactory
    ) {
    }

    public function create(array $configurationPath): BackendLayoutInterface
    {
        $scaling = $this->scalingFactory->getByConfigurationPath($configurationPath);

        $configurationPath[] = 'columns';

        $columns = $this->determineColumns($configurationPath);

        return new BackendLayout($scaling, $columns);
    }

    private function determineColumns(array $configurationPath): array
    {
        $columns = $this->configurationManager->getByPath($configurationPath);
        assert(is_array($columns));

        return array_map(static fn ($column): int => (int) $column, array_keys($columns));
    }
}
