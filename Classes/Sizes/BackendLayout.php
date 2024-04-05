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

final class BackendLayout extends AbstractRootlineElement implements RootlineElementInterface
{
    /**
     * @var int[]
     */
    private array $columns;

    public function __construct(
        protected string $identifier
    ) {
        parent::__construct();

        $this->scalingConfiguration = $this->readConfigurationByPath(
            implode('.', [
                'backendlayouts',
                $this->identifier,
            ])
        );

        $this->determineColumns();
    }

    public function getParent(): ?RootlineElementInterface
    {
        return null;
    }

    public function setParent(RootlineElementInterface $rootlineElement): void
    {

    }

    public function getFinalSize(array $multiplier): array
    {
        return $this->multiplyArray($this->scalingConfiguration->getSizes(), $multiplier);
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    private function determineColumns(): void
    {
        $sizesPath = implode('.', [
            'backendlayouts',
            $this->identifier,
            'columns',
        ]);

        $columns = $this->configurationManager->getByPath($sizesPath);
        assert(is_array($columns));
        $this->columns = array_map(static fn ($column): int => (int) $column, array_keys($columns));
    }
}
