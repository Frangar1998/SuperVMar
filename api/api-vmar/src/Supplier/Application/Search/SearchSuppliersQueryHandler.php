<?php

namespace SuperVMar\Supplier\Application\Search;

use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;
use SuperVMar\Supplier\Domain\Service\SupplierSearcher;

final readonly class SearchSuppliersQueryHandler implements QueryHandler
{
    public function __construct(
        private SupplierSearcher $supplierSearcher
    )
    {
    }

    public function __invoke(SearchSuppliersQuery $query): SuppliersResponse
    {

        $suppliers = $this->supplierSearcher->searchAll();

        return new SuppliersResponse(
            $suppliers->toArray(),
        );
    }
}