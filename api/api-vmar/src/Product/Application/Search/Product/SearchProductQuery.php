<?php

namespace SuperVMar\Product\Application\Search\Product;

use SuperVMar\Shared\Domain\Bus\Query\Query;

final readonly class SearchProductQuery implements Query
{
    public function __construct(
        private string $field,
        private string $value
    )
    {
    }

    public function field(): string
    {
        return $this->field;
    }

    public function value(): string
    {
        return $this->value;
    }
}