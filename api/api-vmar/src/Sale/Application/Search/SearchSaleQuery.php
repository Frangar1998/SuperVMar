<?php

namespace SuperVMar\Sale\Application\Search;

use SuperVMar\Shared\Domain\Bus\Query\Query;

final readonly class SearchSaleQuery implements Query
{
    public function __construct(
        private string $id
    )
    {
    }

    public function id(): string
    {
        return $this->id;
    }
}