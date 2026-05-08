<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Sale\Application;

use SuperVMar\App\Tests\Unit\ApplicationTestCase;
use SuperVMar\Sale\Application\Search\Sales\SalesResponse;
use SuperVMar\Sale\Application\Search\Sales\SearchSalesQuery;
use SuperVMar\Sale\Application\Search\Sales\SearchSalesQueryHandler;
use SuperVMar\Sale\Domain\Sales;
use SuperVMar\Sale\Domain\Service\SaleSearcher;

final class SearchSalesQueryHandlerTest extends ApplicationTestCase
{
    private SaleSearcher $searcher;
    private SearchSalesQueryHandler $handler;

    protected function setUp(): void
    {
        $this->searcher = $this->createMock(SaleSearcher::class);
        $this->handler  = new SearchSalesQueryHandler($this->searcher);
    }

    public function test_calls_search_all_when_no_date_filters(): void
    {
        $this->searcher
            ->expects($this->once())
            ->method('searchAll')
            ->willReturn(new Sales([]));

        $this->searcher->expects($this->never())->method('searchAfterDate');
        $this->searcher->expects($this->never())->method('searchByDateRange');

        ($this->handler)(new SearchSalesQuery());
    }

    public function test_calls_search_after_date_when_only_date_provided(): void
    {
        $this->searcher
            ->expects($this->once())
            ->method('searchAfterDate')
            ->willReturn(new Sales([]));

        $this->searcher->expects($this->never())->method('searchAll');
        $this->searcher->expects($this->never())->method('searchByDateRange');

        ($this->handler)(new SearchSalesQuery(date: '2026-01-01'));
    }

    public function test_calls_search_by_date_range_when_both_dates_provided(): void
    {
        $this->searcher
            ->expects($this->once())
            ->method('searchByDateRange')
            ->willReturn(new Sales([]));

        $this->searcher->expects($this->never())->method('searchAll');
        $this->searcher->expects($this->never())->method('searchAfterDate');

        ($this->handler)(new SearchSalesQuery(date: '2026-01-01', dateTo: '2026-04-01'));
    }

    public function test_returns_sales_response_instance(): void
    {
        $this->searcher->expects($this->once())->method('searchAll')->willReturn(new Sales([]));

        $result = ($this->handler)(new SearchSalesQuery());

        $this->assertInstanceOf(SalesResponse::class, $result);
    }

    public function test_returns_empty_sales_when_no_sales_exist(): void
    {
        $this->searcher->expects($this->once())->method('searchAll')->willReturn(new Sales([]));

        $result = ($this->handler)(new SearchSalesQuery());

        $this->assertSame(['sales' => []], $result->toArray());
    }
}
