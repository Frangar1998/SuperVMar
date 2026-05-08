<?php

namespace SuperVMar\Sale\Application\Search\Sales;

use SuperVMar\Shared\Domain\Bus\Query\Query;

final readonly class SearchSalesQuery implements Query
{
    public function __construct(
        private ?string $date = null,
        private ?string $dateTo = null
    )
    {
    }

    public function date(): ?string
    {
        return $this->date;
    }

    public function dateTo(): ?string
    {
        return $this->dateTo;
    }
}