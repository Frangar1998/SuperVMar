<?php

namespace SuperVMar\Supermarket\Application\Search\Supermarket;

use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;
use SuperVMar\Supermarket\Domain\Service\SupermarketSearcher;

final readonly class SearchSupermarketsQueryHandler implements QueryHandler
{
    public function __construct(
        private SupermarketSearcher $searcher
    ){
    }

    public function __invoke(SearchSupermarketsQuery $query): SupermarketsResponse
    {
        $supermarkets = $this->searcher->searchAll();

        return new SupermarketsResponse(
            $supermarkets->toArray(),
        );
    }
}

