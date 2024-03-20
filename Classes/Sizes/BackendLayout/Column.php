<?php

declare(strict_types=1);

namespace Codappix\ResponsiveImages\Sizes\BackendLayout;

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

use Codappix\ResponsiveImages\Sizes\Multiplier;

class Column
{
    /**
     * @var float[]
     */
    private readonly array $multiplier;

    public function __construct(
        private readonly string $identifier,
        array $data
    ) {
        $this->multiplier = array_map(static fn ($multiplier): float => Multiplier::parse($multiplier), $data['multiplier']);
    }

    public static function make(mixed ...$arguments): self
    {
        return new self(...$arguments);
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return float[]
     */
    public function getMultiplier(): array
    {
        return $this->multiplier;
    }
}
