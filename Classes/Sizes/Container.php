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

use Codappix\ResponsiveImages\Sizes\BackendLayout\Column;

final class Container extends ContentElement
{
    private readonly string $layout;

    private array $columns = [];

    private Column $activeColumn;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->layout = $data['CType'];

        $this->determineColumns();
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getColumn(int $columnPosition): Column
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

    /**
     * @return float[]
     */
    public function getMultiplier(): array
    {
        return $this->getActiveColumn()->getMultiplier();
    }

    /**
     * @return int[]
     */
    public function getSizes(): array
    {
        return $this->getActiveColumn()->getSizes();
    }

    private function determineColumns(): void
    {
        $sizesPath = implode('.', [
            $this->layout,
            'columns',
        ]);

        $columnsByPath = $this->configurationManager->getByPath($sizesPath);
        if (is_iterable($columnsByPath)) {
            foreach ($columnsByPath as $columnIdentifier => $columnData) {
                $this->columns[$columnIdentifier] = new Column($columnIdentifier, $columnData);
            }
        }
    }
}
