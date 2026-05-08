<?php

namespace SuperVMar\Tax\Application\Search;

use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;
use SuperVMar\Tax\Domain\Service\TaxSearcher;

final readonly class SearchTaxesQueryHandler implements QueryHandler
{
    public function __construct(
        private TaxSearcher $taxSearcher
    )
    {
    }

    public function __invoke(SearchTaxesQuery $query): TaxesResponse
    {

        $taxes = $this->taxSearcher->searchAll();

        return new TaxesResponse(
            $taxes->toTableData(),
        );
    }
}