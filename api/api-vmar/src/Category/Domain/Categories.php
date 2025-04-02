<?php

namespace SuperVMar\Category\Domain;

use SuperVMar\Shared\Domain\Collection;

final class Categories extends Collection
{

    protected function type(): string
    {
        return Category::class;
    }

    public static function fromArray(array $categories): self
    {
        return new self(
            array_map(
                fn(array $category) => Category::fromArray($category),
                $categories
            )
        );
    }
}
{

}