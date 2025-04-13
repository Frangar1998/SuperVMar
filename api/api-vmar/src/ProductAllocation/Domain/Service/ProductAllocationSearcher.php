<?php

namespace SuperVMar\ProductAllocation\Domain\Service;

use SuperVMar\ProductAllocation\Domain\ProductAllocation;
use SuperVMar\ProductAllocation\Domain\ProductAllocationRepository;
use SuperVMar\ProductAllocation\Domain\ProductsAllocations;
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

final readonly class ProductAllocationSearcher
{
    public function __construct(
        private ProductAllocationRepository $productAllocationRepository,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function searchBySpace(Id $idSpace): ProductAllocation
    {
        return $this->productAllocationRepository->searchByCriteria(
            new Criteria(
                filters: new Filters(
                    [
                        new Filter(
                            new FilterField(TableNames::TABLE_PRODUCT_ALLOCATION, new FieldName('idSpace')),
                            FilterOperator::EQUAL,
                            new FilterValue($idSpace)
                        )
                    ]
                ),
                select: $this->getSelect(),
                joins: $this->getJoins(),
            )
        )->first();
    }

    /**
     * @throws ItemNotFoundException
     */
    public function searchByProduct(Id $idProduct): ProductAllocation
    {
        return $this->productAllocationRepository->searchByCriteria(
            new Criteria(
                filters: new Filters(
                    [
                        new Filter(
                            new FilterField(TableNames::TABLE_PRODUCT_ALLOCATION, new FieldName('idProduct')),
                            FilterOperator::EQUAL,
                            new FilterValue($idProduct)
                        )
                    ]
                ),
                select: $this->getSelect(),
                joins: $this->getJoins(),
            )
        )->first();
    }

    /**
     * @throws ItemNotFoundException
     */
    public function searchAll(): ProductsAllocations
    {
        return $this->productAllocationRepository->searchByCriteria(
            new Criteria(
                select: $this->getSelect(),
                joins: $this->getJoins(),
            )
        );
    }

    protected function getSelect(): Select
    {
        return new Select(
            new Fields([
                new Field(TableNames::TABLE_PRODUCT_ALLOCATION, new FieldName('idProduct')),
                new Field(TableNames::TABLE_PRODUCT_ALLOCATION, new FieldName('idSpace')),
                new Field(TableNames::TABLE_PRODUCT_ALLOCATION, new FieldName('quantity')),
                new Field(TableNames::TABLE_PRODUCT, new FieldName('name as nameProduct')),
                new Field(TableNames::TABLE_PRODUCT, new FieldName('stock')),
                new Field(TableNames::TABLE_PRODUCT, new FieldName('image')),
                new Field(TableNames::TABLE_SPACE, new FieldName('position')),
                new Field(TableNames::TABLE_SPACE, new FieldName('maxSpots')),
                new Field(TableNames::TABLE_ZONE, new FieldName('id AS idZone')),
                new Field(TableNames::TABLE_ZONE, new FieldName('name as nameZone')),
                new Field(TableNames::TABLE_ZONE, new FieldName('cornerTopLeft')),
                new Field(TableNames::TABLE_ZONE, new FieldName('cornerTopRight')),
                new Field(TableNames::TABLE_ZONE, new FieldName('cornerBottomRight')),
                new Field(TableNames::TABLE_ZONE, new FieldName('cornerBottomLeft')),
            ])
        );
    }

    protected function getJoins(): Joins
    {
        return new Joins(
            [
                new Join(
                    JoinType::INNER,
                    new JoinFirstTable(TableNames::TABLE_PRODUCT_ALLOCATION->value),
                    new JoinSecondTable(TableNames::TABLE_PRODUCT->value),
                    new On(
                        new OnFirstField(TableNames::TABLE_PRODUCT_ALLOCATION, new FieldName('idProduct')),
                        OnOperator::EQUAL,
                        new OnSecondField(TableNames::TABLE_PRODUCT, new FieldName('id'))
                    )
                ),
                new Join(
                    JoinType::INNER,
                    new JoinFirstTable(TableNames::TABLE_PRODUCT_ALLOCATION->value),
                    new JoinSecondTable(TableNames::TABLE_SPACE->value),
                    new On(
                        new OnFirstField(TableNames::TABLE_PRODUCT_ALLOCATION, new FieldName('idSpace')),
                        OnOperator::EQUAL,
                        new OnSecondField(TableNames::TABLE_SPACE, new FieldName('id'))
                    )
                ),
                new Join(
                    JoinType::INNER,
                    new JoinFirstTable(TableNames::TABLE_SPACE->value),
                    new JoinSecondTable(TableNames::TABLE_ZONE->value),
                    new On(
                        new OnFirstField(TableNames::TABLE_SPACE, new FieldName('idZone')),
                        OnOperator::EQUAL,
                        new OnSecondField(TableNames::TABLE_ZONE, new FieldName('id'))
                    )
                )
            ]
        );
    }
}