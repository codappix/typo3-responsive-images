<?php

declare(strict_types=1);

namespace Codappix\ResponsiveImages\Domain\Repository;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Error\Exception;

class ContainerRepository
{
    public function __construct(
        private Connection $connection
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
            ->select('*')
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
