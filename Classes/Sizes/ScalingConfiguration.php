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

class ScalingConfiguration
{
    /**
     * @var float[]
     */
    private array $multiplier = [];

    /**
     * @var int[]
     */
    private array $sizes = [];

    public function __construct(array $configuration)
    {
        if (isset($configuration['multiplier'])) {
            $this->multiplier = array_map(static fn ($multiplier): float => (float) $multiplier, $configuration['multiplier']);
        }

        if (isset($configuration['sizes'])) {
            $this->sizes = array_map(static fn ($size): int => (int) $size, $configuration['sizes']);
        }
    }

    /**
     * @return float[]
     */
    public function getMultiplier(): array
    {
        return $this->multiplier;
    }

    /**
     * @return int[]
     */
    public function getSizes(): array
    {
        return $this->sizes;
    }
}
