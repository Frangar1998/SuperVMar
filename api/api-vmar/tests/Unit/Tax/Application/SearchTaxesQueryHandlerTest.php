<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Tax\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Tax\Application\Search\SearchTaxesQuery;
use SuperVMar\Tax\Application\Search\SearchTaxesQueryHandler;
use SuperVMar\Tax\Application\Search\TaxesResponse;
use SuperVMar\Tax\Domain\Service\TaxSearcher;
use SuperVMar\Tax\Domain\Taxes;

final class SearchTaxesQueryHandlerTest extends TestCase
{
    private TaxSearcher $searcher;
    private SearchTaxesQueryHandler $handler;

    protected function setUp(): void
    {
        $this->searcher = $this->createMock(TaxSearcher::class);
        $this->handler  = new SearchTaxesQueryHandler($this->searcher);
    }

    public function test_returns_taxes_response(): void
    {
        $this->searcher->expects($this->once())->method('searchAll')->willReturn(new Taxes([]));

        $result = ($this->handler)(new SearchTaxesQuery());

        $this->assertInstanceOf(TaxesResponse::class, $result);
    }

    public function test_returns_empty_array_when_no_taxes(): void
    {
        $this->searcher->expects($this->once())->method('searchAll')->willReturn(new Taxes([]));

        $result = ($this->handler)(new SearchTaxesQuery());

        $this->assertSame([], $result->toArray());
    }

    public function test_delegates_to_searcher_search_all(): void
    {
        $this->searcher
            ->expects($this->once())
            ->method('searchAll')
            ->willReturn(new Taxes([]));

        ($this->handler)(new SearchTaxesQuery());
    }

    public function test_returns_populated_response_when_taxes_exist(): void
    {
        $taxes = Taxes::fromArray([
            ['id' => '550e8400-e29b-41d4-a716-000000000020', 'name' => 'IVA 21%', 'percent' => 21.0],
        ]);
        $this->searcher->expects($this->once())->method('searchAll')->willReturn($taxes);

        $result = ($this->handler)(new SearchTaxesQuery());

        $this->assertCount(1, $result->toArray());
        $this->assertSame('IVA 21%', $result->toArray()[0]['name']);
    }
}
