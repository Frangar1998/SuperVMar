<?php

namespace SuperVMar\Supermarket\Application\Search;

use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Supermarket\Domain\Service\SupermarketSearcher;

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
            $supermarket->email()->value(),
            $supermarket->zones()->toArray()
        );
    }
}