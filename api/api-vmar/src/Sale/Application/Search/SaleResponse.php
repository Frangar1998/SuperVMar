<?php

namespace SuperVMar\Sale\Application\Search;

use SuperVMar\Shared\Domain\Bus\Query\Response;

final readonly class SaleResponse implements Response
{
    public function __construct(
        private string $id,
        private float $amount,
        private float $taxesAmount,
        private float $totalAmount,
        private array $lines,
        private string $payMethod,
        private ?string $finishedDate
    )
    {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'taxesAmount' => $this->taxesAmount,
            'totalAmount' => $this->totalAmount,
            'lines' => $this->lines,
            'payMethod' => $this->payMethod,
            'finishedDate' => $this->finishedDate
        ];
    }
}