<?php

namespace SuperVMar\Category\Application\Save;

use SuperVMar\Category\Domain\CategoryRepository;
use SuperVMar\Category\Domain\Service\CategorySearcher;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;

readonly class CategoryUpdater
{
    public function __construct(
        private CategorySearcher $categorySearcher,
        private CategoryRepository $categoryRepository
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function update(
        Id      $id,
        Name    $name
    ): void
    {
        $category = $this->categorySearcher->search($id);
        $category->changeName($name);
        $this->categoryRepository->update($category);
    }
}