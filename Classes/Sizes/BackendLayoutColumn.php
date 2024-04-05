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

final class BackendLayoutColumn extends AbstractRootlineElement implements RootlineElementInterface
{
    public function __construct(
        protected string $identifier,
        protected int $column
    ) {
        parent::__construct();

        $this->scalingConfiguration = $this->readConfigurationByPath(
            implode('.', [
                'backendlayouts',
                $this->identifier,
                'columns',
                (string) $this->column,
            ])
        );
    }

    public function getColumn(): int
    {
        return $this->column;
    }
}
