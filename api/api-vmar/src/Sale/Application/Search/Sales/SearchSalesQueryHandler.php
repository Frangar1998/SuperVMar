<?php

namespace SuperVMar\src\Sale\Application\Search\Sales;

use SuperVMar\Sale\Domain\Service\SaleSearcher;
use SuperVMar\Sale\Domain\ValueObject\FinishedDate;
use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class SearchSalesQueryHandler implements QueryHandler
{
    public function __construct(
        private SaleSearcher $saleSearcher
    )
    {
    }

    public function __invoke(SearchSalesQuery $query): SalesResponse
    {
        $date = new FinishedDate($query->date());

        $sales = $this->saleSearcher->searchAfterDate($date);

        return new SalesResponse(
            $sales->toArray()
        );
    }
}