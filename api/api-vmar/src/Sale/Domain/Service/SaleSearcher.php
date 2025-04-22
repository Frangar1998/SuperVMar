<?php

namespace SuperVMar\Sale\Domain\Service;

use SuperVMar\Sale\Domain\Sale;
use SuperVMar\Sale\Domain\SaleRepository;
use SuperVMar\Sale\Domain\Sales;
use SuperVMar\Sale\Domain\ValueObject\FinishedDate;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Criteria\FieldName;
use SuperVMar\Shared\Domain\Criteria\Filter;
use SuperVMar\Shared\Domain\Criteria\FilterField;
use SuperVMar\Shared\Domain\Criteria\FilterOperator;
use SuperVMar\Shared\Domain\Criteria\Filters;
use SuperVMar\Shared\Domain\Criteria\FilterValue;
use SuperVMar\Shared\Domain\Criteria\Order;
use SuperVMar\Shared\Domain\Criteria\OrderBy;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class SaleSearcher
{
    public function __construct(
        private SaleRepository $saleRepository
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function search(Id $idSale): Sale
    {
        return $this->saleRepository->searchByCriteria(
            new Criteria(
                filters: new Filters(
                    [
                        new Filter(
                            new FilterField(TableNames::TABLE_SALE, new FieldName('id')),
                            FilterOperator::EQUAL,
                            new FilterValue($idSale)
                        )
                    ]
                )
            )
        )->first();
    }

    /**
     * @throws ItemNotFoundException
     */
    public function searchAfterDate(FinishedDate $date): Sales
    {
        return $this->saleRepository->searchByCriteria(
            new Criteria(
                filters: new Filters(
                    [
                        new Filter(
                            new FilterField(TableNames::TABLE_SALE, new FieldName('date')),
                            FilterOperator::GREATER_EQUAL,
                            new FilterValue($date->formatDate())
                        )
                    ]
                ),
                order: Order::createDesc(new OrderBy('date'))
            )
        );
    }

    /**
     * @throws ItemNotFoundException
     */
    public function searchAll(): Sales
    {
        return $this->saleRepository->searchByCriteria(
            new Criteria(
                order: Order::createDesc(new OrderBy('date'))
            )
        );
    }
}