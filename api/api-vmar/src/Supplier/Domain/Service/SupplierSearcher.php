<?php

namespace SuperVMar\Supplier\Domain\Service;

use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Criteria\FieldName;
use SuperVMar\Shared\Domain\Criteria\Filter;
use SuperVMar\Shared\Domain\Criteria\FilterField;
use SuperVMar\Shared\Domain\Criteria\FilterOperator;
use SuperVMar\Shared\Domain\Criteria\Filters;
use SuperVMar\Shared\Domain\Criteria\FilterValue;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Supplier\Domain\Supplier;
use SuperVMar\Supplier\Domain\SupplierRepository;
use SuperVMar\Supplier\Domain\Suppliers;

readonly class SupplierSearcher
{
    public function __construct(
        private SupplierRepository $supplierRepository,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function search(Id $idSupplier): Supplier
    {
        return $this->supplierRepository->searchByCriteria(
            new Criteria(
                filters: new Filters(
                    [
                        new Filter(
                            new FilterField(TableNames::TABLE_SUPPLIER, new FieldName('id')),
                            FilterOperator::EQUAL,
                            new FilterValue($idSupplier)
                        )
                    ]
                )
            )
        )->first();
    }

    /**
     * @throws ItemNotFoundException
     */
    public function searchAll(): Suppliers
    {
        return $this->supplierRepository->searchByCriteria(
            new Criteria()
        );
    }

    /**
     * @throws ItemNotFoundException
     */
    public function checkSuppliedProductsExists(Id $idSupplier): void
    {
        $this->supplierRepository->checkSuppliedProductsExists($idSupplier);
    }
}