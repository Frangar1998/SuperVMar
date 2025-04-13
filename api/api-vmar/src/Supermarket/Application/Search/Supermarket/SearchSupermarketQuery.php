<?php

namespace SuperVMar\Supermarket\Application\Search\Supermarket;

use SuperVMar\Shared\Domain\Bus\Query\Query;

final readonly class SearchSupermarketQuery implements Query
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