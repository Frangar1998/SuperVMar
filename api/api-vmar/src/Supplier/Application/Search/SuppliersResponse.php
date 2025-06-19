<?php

namespace SuperVMar\Supplier\Application\Search;

use SuperVMar\Shared\Domain\Bus\Query\Response;

final readonly class SuppliersResponse implements Response
{
    public function __construct(
        private array $suppliers,
    )
    {
    }

    public function toArray(): array
    {
        return $this->suppliers;
    }
}