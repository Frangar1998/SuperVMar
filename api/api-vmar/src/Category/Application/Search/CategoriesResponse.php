<?php

namespace SuperVMar\Category\Application\Search;

use SuperVMar\Shared\Domain\Bus\Query\Response;

final readonly class CategoriesResponse implements Response
{
    public function __construct(
        private array $categories,
    )
    {
    }

    public function toArray(): array
    {
        return $this->categories;
    }
}