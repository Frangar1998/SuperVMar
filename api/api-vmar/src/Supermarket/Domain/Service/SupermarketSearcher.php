<?php

namespace SuperVMar\Supermarket\Domain\Service;

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
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Supermarket\Domain\Supermarket;
use SuperVMar\Supermarket\Domain\SupermarketRepository;

final readonly class SupermarketSearcher
{
    public function __construct(
        private SupermarketRepository $repository
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function search(Id $idSupermarket): Supermarket
    {
        return $this->repository->searchByCriteria(
            new Criteria(
                filters: new Filters(
                    [
                        new Filter(
                            new FilterField(TableNames::TABLE_SUPERMARKET, new FieldName('id')),
                            FilterOperator::EQUAL,
                            new FilterValue($idSupermarket)
                        )
                    ]
                ),
                select: new Select(
                    new Fields([
                        new Field(TableNames::TABLE_SUPERMARKET, new FieldName('*')),
                        new Field(TableNames::TABLE_ADDRESS, new FieldName('name AS nameAddress')),
                        new Field(TableNames::TABLE_ADDRESS, new FieldName('postalCode')),
                        new Field(TableNames::TABLE_ADDRESS, new FieldName('city')),
                        new Field(TableNames::TABLE_ADDRESS, new FieldName('number')),
                        new Field(TableNames::TABLE_ADDRESS, new FieldName('province')),
                    ])
                ),
                joins: new Joins(
                    [
                        new Join(
                            JoinType::INNER,
                            new JoinFirstTable(TableNames::TABLE_SUPERMARKET->value),
                            new JoinSecondTable(TableNames::TABLE_ADDRESS->value),
                            new On(
                                new OnFirstField(TableNames::TABLE_SUPERMARKET, new FieldName('idAddress')),
                                OnOperator::EQUAL,
                                new OnSecondField(TableNames::TABLE_ADDRESS, new FieldName('id'))
                            )
                        )
                    ]
                )
            )
        );
    }
}