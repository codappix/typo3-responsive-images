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

use Codappix\ResponsiveImages\Configuration\ConfigurationManager;
use Codappix\ResponsiveImages\Sizes\BackendLayout\Column;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class BackendLayout
{
    private array $sizes = [];

    private array $columns = [];

    private Column $activeColumn;

    private readonly ConfigurationManager $configurationManager;

    public function __construct(
        protected string $identifier
    ) {
        $this->configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);

        $this->determineSizes();
        $this->determineColumns();
    }

    public function getSizes(): array
    {
        return $this->sizes;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getColumn(string $columnPosition): Column
    {
        return $this->columns[$columnPosition];
    }

    public function setActiveColumn(Column $column): void
    {
        $this->activeColumn = $column;
    }

    public function getActiveColumn(): Column
    {
        return $this->activeColumn;
    }

    private function determineSizes(): void
    {
        $sizesPath = implode('.', [
            'backendlayouts',
            $this->identifier,
            'sizes',
        ]);

        if (is_array($this->configurationManager->getByPath($sizesPath))) {
            $this->sizes = $this->configurationManager->getByPath($sizesPath);
        }
    }

    private function determineColumns(): array
    {
        $sizesPath = implode('.', [
            'backendlayouts',
            $this->identifier,
            'columns',
        ]);

        $breakpointsByPath = $this->configurationManager->getByPath($sizesPath);

        if (is_iterable($breakpointsByPath)) {
            foreach ($breakpointsByPath as $columnIdentifier => $columnData) {
                $this->columns[$columnIdentifier] = new Column($columnIdentifier, $columnData);
            }
        }

        return $this->columns;
    }
}
