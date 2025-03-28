<?php

namespace SuperVMar\Supermarket\Application\Search;

use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;
use SuperVMar\Supermarket\Domain\Service\SupermarketSearcher;
use SuperVMar\Supermarket\Domain\ValueObject\Id;

final readonly class SearchSupermarketQueryHandler implements QueryHandler
{
    public function __construct(
        private SupermarketSearcher $searcher
    ){
    }

    public function __invoke(SearchSupermarketQuery $query): SupermarketResponse
    {
        $id = new Id($query->id());

        $supermarket = $this->searcher->search($id);

        return new SupermarketResponse(
            $supermarket->id()->value(),
            $supermarket->name()->value(),
            $supermarket->address()->toArray(),
            $supermarket->phone()->value(),
            $supermarket->zones()->toArray()
        );
    }
}