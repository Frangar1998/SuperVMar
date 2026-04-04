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
        if ($query->date() !== null && $query->dateTo() !== null) {
            $sales = $this->saleSearcher->searchByDateRange(
                new FinishedDate($query->date()),
                new FinishedDate($query->dateTo())
            );
        } elseif ($query->date() !== null) {
            $sales = $this->saleSearcher->searchAfterDate(new FinishedDate($query->date()));
        } else {
            $sales = $this->saleSearcher->searchAll();
        }

        return new SalesResponse($sales->toArray());
    }
}