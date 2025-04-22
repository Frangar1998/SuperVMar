<?php

namespace SuperVMar\Product\Domain\Service;

use SuperVMar\Product\Domain\Product;
use SuperVMar\Product\Domain\ProductRepository;
use SuperVMar\Product\Domain\Products;
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

final readonly class ProductSearcher
{
    public function __construct(
        private ProductRepository $productRepository
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function search(Id $idProduct): Product
    {
        return $this->productRepository->searchByCriteria(
            new Criteria(
                filters: new Filters(
                    [
                        new Filter(
                            new FilterField(TableNames::TABLE_PRODUCT, new FieldName('id')),
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
    public function searchByField(string $field, string $value): Product
    {
        return $this->productRepository->searchByCriteria(
            new Criteria(
                filters: new Filters(
                    [
                        new Filter(
                            new FilterField(TableNames::TABLE_PRODUCT, new FieldName($field)),
                            FilterOperator::EQUAL,
                            new FilterValue($value)
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
    public function searchAll(): Products
    {
        return $this->productRepository->searchByCriteria(
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
                new Field(TableNames::TABLE_PRODUCT, new FieldName('id')),
                new Field(TableNames::TABLE_PRODUCT, new FieldName('name')),
                new Field(TableNames::TABLE_PRODUCT, new FieldName('price')),
                new Field(TableNames::TABLE_PRODUCT, new FieldName('ean')),
                new Field(TableNames::TABLE_PRODUCT, new FieldName('stock')),
                new Field(TableNames::TABLE_PRODUCT, new FieldName('image')),
                new Field(TableNames::TABLE_PRODUCT, new FieldName('active')),
                new Field(TableNames::TABLE_TAX, new FieldName('id AS idTax')),
                new Field(TableNames::TABLE_TAX, new FieldName('name AS nameTax')),
                new Field(TableNames::TABLE_TAX, new FieldName('percent')),
                new Field(TableNames::TABLE_CATEGORY, new FieldName('id AS idCategory')),
                new Field(TableNames::TABLE_CATEGORY, new FieldName('name AS nameCategory')),
                new Field(TableNames::TABLE_SUPPLIER, new FieldName('id AS idSupplier')),
                new Field(TableNames::TABLE_SUPPLIER, new FieldName('name AS nameSupplier')),
            ])
        );
    }

    protected function getJoins(): Joins
    {
        return new Joins(
            [
                new Join(
                    JoinType::INNER,
                    new JoinFirstTable(TableNames::TABLE_PRODUCT->value),
                    new JoinSecondTable(TableNames::TABLE_TAX->value),
                    new On(
                        new OnFirstField(TableNames::TABLE_PRODUCT, new FieldName('idTax')),
                        OnOperator::EQUAL,
                        new OnSecondField(TableNames::TABLE_TAX, new FieldName('id'))
                    )
                ),
                new Join(
                    JoinType::INNER,
                    new JoinFirstTable(TableNames::TABLE_PRODUCT->value),
                    new JoinSecondTable(TableNames::TABLE_CATEGORY->value),
                    new On(
                        new OnFirstField(TableNames::TABLE_PRODUCT, new FieldName('idCategory')),
                        OnOperator::EQUAL,
                        new OnSecondField(TableNames::TABLE_CATEGORY, new FieldName('id'))
                    )
                ),
                new Join(
                    JoinType::INNER,
                    new JoinFirstTable(TableNames::TABLE_PRODUCT->value),
                    new JoinSecondTable(TableNames::TABLE_SUPPLIER->value),
                    new On(
                        new OnFirstField(TableNames::TABLE_PRODUCT, new FieldName('idSupplier')),
                        OnOperator::EQUAL,
                        new OnSecondField(TableNames::TABLE_SUPPLIER, new FieldName('id'))
                    )
                )
            ]
        );
    }
}