<?php

declare(strict_types=1);

namespace Codappix\ResponsiveImages\Domain\Repository;

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

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Error\Exception;

final class ContainerRepository
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    public function findByIdentifier(int $identifier): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $rawData = $queryBuilder
            ->select('uid', 'colPos', 'CType', 'tx_container_parent')
            ->from('tt_content')
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($identifier, Connection::PARAM_INT)))
            ->executeQuery()
            ->fetchAssociative()
        ;

        if ($rawData === false) {
            throw new Exception("Content element '" . $identifier . "' not found.");
        }

        return $rawData;
    }
}
