<?php

namespace SuperVMar\Tax\Application\Search;

use SuperVMar\Shared\Domain\Bus\Query\Response;

final readonly class TaxesResponse implements Response
{
    public function __construct(
        private array $taxes,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'taxes' => $this->taxes,
        ];
    }
}