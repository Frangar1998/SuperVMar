<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Category\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Category\Application\Search\CategoriesResponse;
use SuperVMar\Category\Application\Search\SearchCategoriesQuery;
use SuperVMar\Category\Application\Search\SearchCategoriesQueryHandler;
use SuperVMar\Category\Domain\Categories;
use SuperVMar\Category\Domain\Service\CategorySearcher;

final class SearchCategoriesQueryHandlerTest extends TestCase
{
    private CategorySearcher $searcher;
    private SearchCategoriesQueryHandler $handler;

    protected function setUp(): void
    {
        $this->searcher = $this->createMock(CategorySearcher::class);
        $this->handler  = new SearchCategoriesQueryHandler($this->searcher);
    }

    public function test_returns_categories_response(): void
    {
        $this->searcher->expects($this->once())->method('searchAll')->willReturn(new Categories([]));

        $result = ($this->handler)(new SearchCategoriesQuery());

        $this->assertInstanceOf(CategoriesResponse::class, $result);
    }

    public function test_returns_empty_array_when_no_categories(): void
    {
        $this->searcher->expects($this->once())->method('searchAll')->willReturn(new Categories([]));

        $result = ($this->handler)(new SearchCategoriesQuery());

        $this->assertSame([], $result->toArray());
    }

    public function test_delegates_to_searcher_search_all(): void
    {
        $this->searcher
            ->expects($this->once())
            ->method('searchAll')
            ->willReturn(new Categories([]));

        ($this->handler)(new SearchCategoriesQuery());
    }

    public function test_returns_populated_response_when_categories_exist(): void
    {
        $categories = Categories::fromArray([
            ['id' => '550e8400-e29b-41d4-a716-000000000030', 'name' => 'Lácteos'],
        ]);
        $this->searcher->expects($this->once())->method('searchAll')->willReturn($categories);

        $result = ($this->handler)(new SearchCategoriesQuery());

        $this->assertCount(1, $result->toArray());
        $this->assertSame('Lácteos', $result->toArray()[0]['name']);
    }
}
