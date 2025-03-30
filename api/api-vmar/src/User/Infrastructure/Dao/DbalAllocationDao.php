<?php

namespace SuperVMar\User\Infrastructure\Dao;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Criteria\Field;
use SuperVMar\Shared\Domain\Criteria\FieldName;
use SuperVMar\Shared\Domain\Criteria\Fields;
use SuperVMar\Shared\Domain\Criteria\Filter;
use SuperVMar\Shared\Domain\Criteria\FilterField;
use SuperVMar\Shared\Domain\Criteria\FilterOperator;
use SuperVMar\Shared\Domain\Criteria\Filters;
use SuperVMar\Shared\Domain\Criteria\FilterValue;
use SuperVMar\Shared\Domain\Criteria\Join;
use SuperVMar\Shared\Domain\Criteria\JoinFirstTable;
use SuperVMar\Shared\Domain\Criteria\Joins;
use SuperVMar\Shared\Domain\Criteria\JoinSecondTable;
use SuperVMar\Shared\Domain\Criteria\JoinType;
use SuperVMar\Shared\Domain\Criteria\On;
use SuperVMar\Shared\Domain\Criteria\OnFirstField;
use SuperVMar\Shared\Domain\Criteria\OnOperator;
use SuperVMar\Shared\Domain\Criteria\OnSecondField;
use SuperVMar\Shared\Domain\Criteria\Select;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Infrastructure\Doctrine\DbalCriteriaConverter;
use Throwable;

final readonly class DbalAllocationDao
{
    private const string TABLE_WORKER_ALLOCATION = TableNames::TABLE_WORKER_ALLOCATION->value;
    private const string TABLE_JOB = TableNames::TABLE_JOB->value;
    private const string TABLE_SUPERMARKET = TableNames::TABLE_JOB->value;

    public function __construct(
        private Connection $connection,
        private DbalCriteriaConverter $dbalCriteriaConverter
    )
    {
    }

    /**
     * @throws InternalErrorException
     */
    public function search(Id $idUser): array
    {
        $criteria = new Criteria(
            filters: new Filters(
                [
                    new Filter(
                        new FilterField(TableNames::TABLE_WORKER_ALLOCATION, new FieldName('idUser')),
                        FilterOperator::EQUAL,
                        new FilterValue($idUser)
                    )
                ]
            ),
            select: new Select(
                new Fields([
                    new Field(TableNames::TABLE_WORKER_ALLOCATION, new FieldName('idSupermarket')),
                    new Field(TableNames::TABLE_WORKER_ALLOCATION, new FieldName('idJob')),
                    new Field(TableNames::TABLE_JOB, new FieldName('name as nameJob')),
                    new Field(TableNames::TABLE_SUPERMARKET, new FieldName('name as nameSupermarket')),
                ])
            ),
            joins: new Joins(
                [
                    new Join(
                        JoinType::INNER,
                        new JoinFirstTable(TableNames::TABLE_WORKER_ALLOCATION->value),
                        new JoinSecondTable(TableNames::TABLE_JOB->value),
                        new On(
                            new OnFirstField(TableNames::TABLE_WORKER_ALLOCATION, new FieldName('idJob')),
                            OnOperator::EQUAL,
                            new OnSecondField(TableNames::TABLE_JOB, new FieldName('id'))
                        )
                    ),
                    new Join(
                        JoinType::INNER,
                        new JoinFirstTable(TableNames::TABLE_WORKER_ALLOCATION->value),
                        new JoinSecondTable(TableNames::TABLE_SUPERMARKET->value),
                        new On(
                            new OnFirstField(TableNames::TABLE_WORKER_ALLOCATION, new FieldName('idSupermarket')),
                            OnOperator::EQUAL,
                            new OnSecondField(TableNames::TABLE_SUPERMARKET, new FieldName('id'))
                        )
                    )
                ]
            )
        );
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $allocations = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$allocations) {
            return [];
        }

        return $allocations;
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