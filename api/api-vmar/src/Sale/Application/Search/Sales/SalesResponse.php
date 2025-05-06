<?php

namespace SuperVMar\Sale\Application\Search\Sales;

use SuperVMar\Shared\Domain\Bus\Query\Response;

final readonly class SalesResponse implements Response
{
    public function __construct(
        private array $sales
    )
    {
    }

    public function toArray(): array
    {
        return [
            'sales' => $this->sales,
        ];
    }
}