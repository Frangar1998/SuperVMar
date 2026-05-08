<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Integration\Sale\Infrastructure;

use SuperVMar\App\Tests\Fixtures\IntegrationFixtures;
use SuperVMar\App\Tests\Integration\Shared\Infrastructure\DbalTestCase;
use SuperVMar\Sale\Domain\Sale;
use SuperVMar\Sale\Domain\SaleRepository;
use SuperVMar\Sale\Domain\Service\SaleSearcher;
use SuperVMar\Sale\Domain\ValueObject\FinishedDate;
use SuperVMar\Sale\Infrastructure\DbalSaleRepository;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

final class DbalSaleRepositoryTest extends DbalTestCase
{
    private SaleRepository $repository;
    private SaleSearcher $searcher;
    private IntegrationFixtures $fixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(DbalSaleRepository::class);
        $this->searcher   = self::getContainer()->get(SaleSearcher::class);
        $this->fixtures   = new IntegrationFixtures($this->connection);

        $this->fixtures->loadCatalog();
        $this->fixtures->loadProduct();
    }


    public function test_insert_persists_sale_row(): void
    {
        $this->repository->insert($this->buildActiveSale());

        $row = $this->findById('sale', IntegrationFixtures::SALE_ID);
        $this->assertNotFalse($row);
        $this->assertSame(IntegrationFixtures::SALE_ID, $row['id']);
    }

    public function test_insert_persists_sale_line(): void
    {
        $this->repository->insert($this->buildActiveSale());

        $lineCount = $this->countRows('sale_line', ['idSale' => IntegrationFixtures::SALE_ID]);
        $this->assertSame(1, $lineCount);
    }

    public function test_insert_throws_on_duplicate_id(): void
    {
        $sale = $this->buildActiveSale();
        $this->repository->insert($sale);

        $this->expectException(DuplicateItemException::class);
        $this->repository->insert($sale);
    }


    public function test_update_changes_sale_amount(): void
    {
        $this->fixtures->loadSale();

        $updatedSale = Sale::fromArray([
            'id'          => IntegrationFixtures::SALE_ID,
            'amount'      => 2.58,
            'taxes'       => 0.54,
            'totalAmount' => 3.12,
            'payMethod'   => 'cash',
            'date'        => '2026-04-21',
            'lines'       => $this->buildLineArray(2),
        ]);

        $this->repository->update($updatedSale);

        $row = $this->findById('sale', IntegrationFixtures::SALE_ID);
        $this->assertEqualsWithDelta(2.58, (float) $row['amount'], 0.001);
    }


    public function test_delete_removes_sale_row(): void
    {
        $this->fixtures->loadSale();

        $this->repository->delete(new Id(IntegrationFixtures::SALE_ID));

        $this->assertFalse($this->findById('sale', IntegrationFixtures::SALE_ID));
    }

    public function test_delete_removes_sale_lines(): void
    {
        $this->fixtures->loadSale();

        $this->repository->delete(new Id(IntegrationFixtures::SALE_ID));

        $this->assertSame(
            0,
            $this->countRows('sale_line', ['idSale' => IntegrationFixtures::SALE_ID])
        );
    }


    public function test_search_by_id_returns_correct_sale(): void
    {
        $this->fixtures->loadSale();

        $sale = $this->searcher->search(new Id(IntegrationFixtures::SALE_ID));

        $this->assertSame(IntegrationFixtures::SALE_ID, $sale->id()->value());
    }

    public function test_search_all_returns_inserted_sale(): void
    {
        $this->fixtures->loadSale();

        $sales = $this->searcher->searchAll();

        $ids = array_map(fn ($s) => $s->id()->value(), $sales->items());
        $this->assertContains(IntegrationFixtures::SALE_ID, $ids);
    }

    public function test_search_after_date_returns_matching_sales(): void
    {
        $this->fixtures->loadSale();

        $sales = $this->searcher->searchAfterDate(new FinishedDate('2026-01-01'));

        $ids = array_map(fn ($s) => $s->id()->value(), $sales->items());
        $this->assertContains(IntegrationFixtures::SALE_ID, $ids);
    }

    public function test_search_after_date_throws_when_no_sales_found(): void
    {
        $this->fixtures->loadSale();

        $this->expectException(ItemNotFoundException::class);
        $this->searcher->searchAfterDate(new FinishedDate('2027-01-01'));
    }

    public function test_search_by_date_range_returns_matching_sales(): void
    {
        $this->fixtures->loadSale();

        $sales = $this->searcher->searchByDateRange(
            new FinishedDate('2026-01-01'),
            new FinishedDate('2026-12-31'),
        );

        $ids = array_map(fn ($s) => $s->id()->value(), $sales->items());
        $this->assertContains(IntegrationFixtures::SALE_ID, $ids);
    }

    public function test_search_by_id_throws_when_not_found(): void
    {
        $this->expectException(ItemNotFoundException::class);

        $this->searcher->search(new Id('c0000000-0000-0000-0000-000000000099'));
    }


    private function buildActiveSale(): Sale
    {
        return Sale::fromArray([
            'id'          => IntegrationFixtures::SALE_ID,
            'amount'      => 1.29,
            'taxes'       => 0.27,
            'totalAmount' => 1.56,
            'payMethod'   => 'cash',
            'date'        => null,
            'lines'       => $this->buildLineArray(1),
        ]);
    }

    private function buildLineArray(int $quantity): array
    {
        return [
            [
                'id'          => IntegrationFixtures::SALE_LINE_ID,
                'amount'      => 1.29,
                'quantity'    => $quantity,
                'idProduct'   => IntegrationFixtures::PRODUCT_ID,
                'nameProduct' => 'Leche Entera',
                'price'       => IntegrationFixtures::PRODUCT_PRICE,
                'ean'         => IntegrationFixtures::PRODUCT_EAN,
                'idTax'       => IntegrationFixtures::TAX_ID,
                'nameTax'     => 'IVA 21%',
                'percent'     => IntegrationFixtures::TAX_PCT,
            ],
        ];
    }
}
