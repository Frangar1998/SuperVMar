<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Supplier\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Supplier\Application\Search\SearchSuppliersQuery;
use SuperVMar\Supplier\Application\Search\SearchSuppliersQueryHandler;
use SuperVMar\Supplier\Application\Search\SuppliersResponse;
use SuperVMar\Supplier\Domain\Service\SupplierSearcher;
use SuperVMar\Supplier\Domain\Suppliers;

final class SearchSuppliersQueryHandlerTest extends TestCase
{
    private SupplierSearcher $searcher;
    private SearchSuppliersQueryHandler $handler;

    protected function setUp(): void
    {
        $this->searcher = $this->createMock(SupplierSearcher::class);
        $this->handler  = new SearchSuppliersQueryHandler($this->searcher);
    }

    public function test_returns_suppliers_response(): void
    {
        $this->searcher->expects($this->once())->method('searchAll')->willReturn(new Suppliers([]));

        $result = ($this->handler)(new SearchSuppliersQuery());

        $this->assertInstanceOf(SuppliersResponse::class, $result);
    }

    public function test_returns_empty_array_when_no_suppliers(): void
    {
        $this->searcher->expects($this->once())->method('searchAll')->willReturn(new Suppliers([]));

        $result = ($this->handler)(new SearchSuppliersQuery());

        $this->assertSame([], $result->toArray());
    }

    public function test_delegates_to_searcher_search_all(): void
    {
        $this->searcher
            ->expects($this->once())
            ->method('searchAll')
            ->willReturn(new Suppliers([]));

        ($this->handler)(new SearchSuppliersQuery());
    }

    public function test_returns_populated_response_when_suppliers_exist(): void
    {
        $suppliers = Suppliers::fromArray([
            [
                'id'      => '550e8400-e29b-41d4-a716-000000000040',
                'name'    => 'Proveedor Test',
                'phone'   => '600000000',
                'email'   => 'proveedor@test.com',
                'contact' => 'Contacto Test',
            ],
        ]);
        $this->searcher->expects($this->once())->method('searchAll')->willReturn($suppliers);

        $result = ($this->handler)(new SearchSuppliersQuery());

        $this->assertCount(1, $result->toArray());
        $this->assertSame('Proveedor Test', $result->toArray()[0]['name']);
    }
}
