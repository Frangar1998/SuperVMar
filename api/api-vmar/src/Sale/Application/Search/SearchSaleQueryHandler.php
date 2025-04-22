<?php

namespace SuperVMar\Sale\Application\Search;

use SuperVMar\Sale\Domain\Service\SaleSearcher;
use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class SearchSaleQueryHandler implements QueryHandler
{
    public function __construct(
        private SaleSearcher $saleSearcher
    )
    {
    }

    public function __invoke(SearchSaleQuery $query): SaleResponse
    {
        $id = new Id($query->id());

        $sale = $this->saleSearcher->search($id);

        return new SaleResponse(
            $sale->id()->value(),
            $sale->amount()->value(),
            $sale->taxesAmount()->value(),
            $sale->totalAmount()->value(),
            $sale->lines()->toArray(),
            $sale->payMethod()->value,
            $sale->finishedDate()?->formatDate()
        );
    }
}