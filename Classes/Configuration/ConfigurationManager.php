<?php

declare(strict_types=1);

namespace Codappix\ResponsiveImages\Configuration;

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

use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * Helper class to get all extension specific settings.
 */
final class ConfigurationManager
{
    public function __construct(
        private array $settings
    ) {
    }

    public function get(): array
    {
        return $this->settings;
    }

    public function isValidPath(array|string $path): bool
    {
        return ArrayUtility::isValidPath($this->settings, $path);
    }

    public function getByPath(array|string $path): mixed
    {
        return ArrayUtility::getValueByPath($this->settings, $path);
    }
}
