<?php

namespace SuperVMar\Category\Application\Search;

use SuperVMar\Category\Domain\Service\CategorySearcher;
use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;

final readonly class SearchCategoriesQueryHandler implements QueryHandler
{
    public function __construct(
        private CategorySearcher $categorySearcher
    )
    {
    }

    public function __invoke(SearchCategoriesQuery $query): CategoriesResponse
    {

        $categories = $this->categorySearcher->searchAll();

        return new CategoriesResponse(
            $categories->toArray(),
        );
    }
}