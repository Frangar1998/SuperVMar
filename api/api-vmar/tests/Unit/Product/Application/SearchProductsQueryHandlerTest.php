<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Product\Application;

use SuperVMar\App\Tests\Unit\ApplicationTestCase;
use SuperVMar\Product\Application\Search\Products\ProductsResponse;
use SuperVMar\Product\Application\Search\Products\SearchProductsQuery;
use SuperVMar\Product\Application\Search\Products\SearchProductsQueryHandler;
use SuperVMar\Product\Domain\Products;
use SuperVMar\Product\Domain\Service\ProductSearcher;

final class SearchProductsQueryHandlerTest extends ApplicationTestCase
{
    private ProductSearcher $searcher;
    private SearchProductsQueryHandler $handler;

    protected function setUp(): void
    {
        $this->searcher = $this->createMock(ProductSearcher::class);
        $this->handler  = new SearchProductsQueryHandler($this->searcher);
    }

    public function test_returns_products_response(): void
    {
        $this->searcher
            ->expects($this->once())
            ->method('searchAll')
            ->willReturn(new Products([]));

        $result = ($this->handler)(new SearchProductsQuery());

        $this->assertInstanceOf(ProductsResponse::class, $result);
    }

    public function test_delegates_to_searcher_search_all(): void
    {
        $this->searcher
            ->expects($this->once())
            ->method('searchAll')
            ->willReturn(new Products([]));

        ($this->handler)(new SearchProductsQuery());
    }

    public function test_returns_empty_array_when_no_products(): void
    {
        $this->searcher->expects($this->once())->method('searchAll')->willReturn(new Products([]));

        $result = ($this->handler)(new SearchProductsQuery());

        $this->assertSame([], $result->toArray());
    }
}
