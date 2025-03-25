<?php

namespace SuperVMar\Shared\Infrastructure\Doctrine;

use Doctrine\DBAL\Query\QueryBuilder;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Criteria\Filter;
use SuperVMar\Shared\Domain\Criteria\Join;
use SuperVMar\Shared\Domain\Criteria\JoinType;

final class DbalCriteriaConverter
{
    protected string $tableName;
    protected Criteria $criteria;
    protected QueryBuilder $queryBuilder;

    public function convert(string $tableName, Criteria $criteria, QueryBuilder $queryBuilder): QueryBuilder
    {
        $this->tableName = $tableName;
        $this->criteria = $criteria;
        $this->queryBuilder = $queryBuilder;
        return $this->buildQuery();
    }

    protected function buildQuery(): QueryBuilder
    {
        $select = ['*'];
        if ($this->criteria->hasSelect()) {
            $select = $this->criteria->select()->fields();
        }
        $queryBuilder = $this->queryBuilder
            ->select(...$select)
            ->from($this->tableName);

        $filters = $this->criteria->filters();
        $paramIndex = 0;
        $params = [];
        /** @var Filter $filter */
        foreach ($filters->items() as $filter) {
            $field = $filter->field();
            $value = $filter->value()->value();
            $operator = $filter->operator()->value;
            $params[$paramIndex] = $value;

            if ($paramIndex === 0) {
                $queryBuilder = $queryBuilder->where(
                    sprintf('%s %s ?', $field, $operator)
                );
            } else {
                $queryBuilder = $queryBuilder->andWhere(
                    sprintf('%s %s ?', $field, $operator)
                );
            }
            ++$paramIndex;
        }

        $queryBuilder = $queryBuilder->setParameters($params);

        if ($this->criteria->hasJoins()) {
            $joins = $this->criteria->joins();
            /** @var Join $join */
            foreach ($joins->items() as $join) {
                $type = $join->type();
                $firstTable = $join->firstTable()->value();
                $secondTable = $join->secondTable()->value();
                $onFirstField = $join->on()->firstField();
                $onOperator = $join->on()->operator()->value;
                $onSecondField = $join->on()->secondField();

                if ($type === JoinType::INNER) {
                    $queryBuilder = $queryBuilder->innerJoin(
                        $firstTable,
                        $secondTable,
                        $secondTable,
                        sprintf('%s %s %s', $onFirstField, $onOperator, $onSecondField)
                    );
                }

                if ($type === JoinType::LEFT) {
                    $queryBuilder = $queryBuilder->leftJoin(
                        $firstTable,
                        $secondTable,
                        $secondTable,
                        sprintf('%s %s %s', $onFirstField, $onOperator, $onSecondField)
                    );
                }

                if ($type === JoinType::RIGHT) {
                    $queryBuilder = $queryBuilder->rightJoin(
                        $firstTable,
                        $secondTable,
                        $secondTable,
                        sprintf('%s %s %s', $onFirstField, $onOperator, $onSecondField)
                    );
                }
            }
        }

        if ($this->criteria->hasOrder()) {
            $queryBuilder = $queryBuilder->orderBy(
                    $this->criteria->order()->orderBy()->value(),
                    $this->criteria->order()->orderType()->value
            );
        }

        if ($this->criteria->hasOffset()) {
            $queryBuilder = $queryBuilder->setFirstResult($this->criteria->offset());
        }

        if ($this->criteria->hasLimit()) {
            $queryBuilder = $queryBuilder->setMaxResults($this->criteria->limit());
        }

        return $queryBuilder;
    }
}