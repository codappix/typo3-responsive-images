<?php

declare(strict_types=1);

namespace Codappix\ResponsiveImages\Domain\Repository;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Error\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ContainerRepository
{
    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    public function findByIdentifier(int $identifier): array
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
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
