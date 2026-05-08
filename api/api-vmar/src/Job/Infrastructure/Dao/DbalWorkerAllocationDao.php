<?php

namespace SuperVMar\Job\Infrastructure\Dao;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Criteria\FieldName;
use SuperVMar\Shared\Domain\Criteria\Filter;
use SuperVMar\Shared\Domain\Criteria\FilterField;
use SuperVMar\Shared\Domain\Criteria\FilterOperator;
use SuperVMar\Shared\Domain\Criteria\Filters;
use SuperVMar\Shared\Domain\Criteria\FilterValue;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Infrastructure\Doctrine\DbalCriteriaConverter;
use Throwable;

final readonly class DbalWorkerAllocationDao
{
    private const string TABLE_WORKER_ALLOCATION = TableNames::TABLE_WORKER_ALLOCATION->value;

    public function __construct(
        private Connection $connection,
        private DbalCriteriaConverter $dbalCriteriaConverter,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     * @throws InternalErrorException
     */
    public function checkAllocationsExists(Id $idJob): void
    {
        try {
            $query = $this->buildQueryByCriteria(
                new Criteria(
                    filters: new Filters(
                        [
                            new Filter(
                                new FilterField(TableNames::TABLE_WORKER_ALLOCATION, new FieldName('idJob')),
                                FilterOperator::EQUAL,
                                new FilterValue($idJob)
                            )
                        ]
                    )
                )
            );
            $workerAllocations = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$workerAllocations) {
            throw new ItemNotFoundException('', []);
        }
    }

    private function buildQueryByCriteria(Criteria $criteria): QueryBuilder
    {
        return $this->dbalCriteriaConverter->convert(
            self::TABLE_WORKER_ALLOCATION,
            $criteria,
            $this->connection->createQueryBuilder()
        );
    }
}