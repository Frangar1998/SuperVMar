<?php

namespace SuperVMar\Sale\Application\Search\Sales;

use SuperVMar\Shared\Domain\Bus\Query\Query;

final readonly class SearchSalesQuery implements Query
{
    public function __construct(
        private string $date
    )
    {
    }

    public function date(): string
    {
        return $this->date;
    }
}