<?php

namespace SuperVMar\Sale\Application\Search\Sales;

use SuperVMar\Sale\Domain\Service\SaleSearcher;
use SuperVMar\Sale\Domain\ValueObject\FinishedDate;
use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;

final readonly class SearchSalesQueryHandler implements QueryHandler
{
    public function __construct(
        private SaleSearcher $saleSearcher
    )
    {
    }

    public function __invoke(SearchSalesQuery $query): SalesResponse
    {
        $sales = $query->date() !== null
            ? $this->saleSearcher->searchAfterDate(new FinishedDate($query->date()))
            : $this->saleSearcher->searchAll();

        return new SalesResponse(
            $sales->toArray()
        );
    }
}