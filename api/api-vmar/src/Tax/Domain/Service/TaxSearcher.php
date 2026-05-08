<?php

namespace SuperVMar\Tax\Domain\Service;

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
use SuperVMar\Tax\Domain\Tax;
use SuperVMar\Tax\Domain\Taxes;
use SuperVMar\Tax\Domain\TaxRepository;

readonly class TaxSearcher
{
    public function __construct(
        private TaxRepository $taxRepository,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function search(Id $idTax): Tax
    {
        return $this->taxRepository->searchByCriteria(
            new Criteria(
                filters: new Filters(
                    [
                        new Filter(
                            new FilterField(TableNames::TABLE_TAX, new FieldName('id')),
                            FilterOperator::EQUAL,
                            new FilterValue($idTax)
                        )
                    ]
                )
            )
        )->first();
    }

    /**
     * @throws ItemNotFoundException
     */
    public function searchAll(): Taxes
    {
        return $this->taxRepository->searchByCriteria(
            new Criteria()
        );
    }

    /**
     * @throws ItemNotFoundException
     */
    public function checkTaxedProductsExists(Id $idTax): void
    {
        $this->taxRepository->checkTaxedProductsExists($idTax);
    }
}