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

use Exception;

abstract class AbstractContentElement extends AbstractRootlineElement implements ContentElementInterface
{
    protected int $colPos;

    protected string $contentType;

    protected RootlineElementInterface $parent;

    public function __construct(
        protected array $data
    ) {
        parent::__construct();

        $this->contentType = $data['CType'];
        $this->colPos = $data['colPos'];
        $this->data = $data;
    }

    public function getData(?string $dataIdentifier = null): mixed
    {
        if ($dataIdentifier === null) {
            return $this->data;
        }

        if (isset($this->data[$dataIdentifier]) === false) {
            throw new Exception('No data found for key ' . $dataIdentifier . ' in $this->data.');
        }

        return $this->data[$dataIdentifier];
    }

    public function getColPos(): int
    {
        return $this->colPos;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }
}
