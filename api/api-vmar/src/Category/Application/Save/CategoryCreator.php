<?php

namespace SuperVMar\Category\Application\Save;

use SuperVMar\Category\Domain\Category;
use SuperVMar\Category\Domain\CategoryRepository;
use SuperVMar\Category\Domain\ValueObject\Name;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class CategoryCreator
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    )
    {
    }

    public function create(
        Id      $id,
        Name    $name
    ): void
    {
        $this->categoryRepository->insert(
            Category::create(
                $id,
                $name
            )
        );

    }
}