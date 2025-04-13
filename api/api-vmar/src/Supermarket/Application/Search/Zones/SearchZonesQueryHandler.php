<?php

namespace SuperVMar\Supermarket\Application\Search\Zones;

use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Supermarket\Domain\Service\SupermarketSearcher;

final readonly class SearchZonesQueryHandler implements QueryHandler
{
    public function __construct(
        private SupermarketSearcher $searcher
    ){
    }

    public function __invoke(SearchZonesQuery $query): ZonesResponse
    {
        $idSupermarket = new Id($query->idSupermarket());

        $supermarket = $this->searcher->search($idSupermarket);

        return new ZonesResponse(
            $supermarket->zones()->toArray()
        );
    }
}