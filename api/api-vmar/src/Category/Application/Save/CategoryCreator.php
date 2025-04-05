<?php

namespace SuperVMar\Category\Application\Save;

use SuperVMar\Category\Domain\Category;
use SuperVMar\Category\Domain\CategoryRepository;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;

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