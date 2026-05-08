<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Integration\Product\Infrastructure;

use SuperVMar\App\Tests\Fixtures\IntegrationFixtures;
use SuperVMar\App\Tests\Integration\Shared\Infrastructure\DbalTestCase;
use SuperVMar\Product\Domain\ProductRepository;
use SuperVMar\Product\Domain\Product;
use SuperVMar\Product\Domain\Service\ProductSearcher;
use SuperVMar\Product\Infrastructure\DbalProductRepository;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Product\Domain\ValueObject\Stock;
use SuperVMar\Product\Domain\Exception\ProductEanAlreadyExistsException;

final class DbalProductRepositoryTest extends DbalTestCase
{
    private ProductRepository $repository;
    private ProductSearcher $searcher;
    private IntegrationFixtures $fixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(DbalProductRepository::class);
        $this->searcher   = self::getContainer()->get(ProductSearcher::class);
        $this->fixtures   = new IntegrationFixtures($this->connection);
        $this->fixtures->loadCatalog();
    }


    public function test_insert_persists_product_row(): void
    {
        $this->repository->insert($this->buildProduct());

        $row = $this->findById('product', IntegrationFixtures::PRODUCT_ID);
        $this->assertNotFalse($row);
        $this->assertSame('Leche Entera', $row['name']);
        $this->assertEqualsWithDelta(IntegrationFixtures::PRODUCT_PRICE, (float) $row['price'], 0.001);
        $this->assertSame(IntegrationFixtures::PRODUCT_STOCK, (int) $row['stock']);
    }

    public function test_insert_throws_on_duplicate_id(): void
    {
        $product = $this->buildProduct();
        $this->repository->insert($product);

        $this->expectException(ProductEanAlreadyExistsException::class);
        $this->repository->insert($product);
    }

    public function test_insert_creates_price_history_entry(): void
    {
        $this->repository->insert($this->buildProduct());

        $count = $this->countRows('price_history', ['idProduct' => IntegrationFixtures::PRODUCT_ID]);
        $this->assertGreaterThanOrEqual(1, $count);
    }


    public function test_update_changes_product_name(): void
    {
        $this->fixtures->loadProduct();
        $this->repository->update($this->buildProduct(name: 'Leche Desnatada'));

        $row = $this->findById('product', IntegrationFixtures::PRODUCT_ID);
        $this->assertSame('Leche Desnatada', $row['name']);
    }

    public function test_update_changes_product_price(): void
    {
        $this->fixtures->loadProduct();
        $this->repository->update($this->buildProduct(price: 2.49));

        $row = $this->findById('product', IntegrationFixtures::PRODUCT_ID);
        $this->assertEqualsWithDelta(2.49, (float) $row['price'], 0.001);
    }


    public function test_update_stock_changes_only_stock_field(): void
    {
        $this->fixtures->loadProduct();

        $this->repository->updateStock(
            new Id(IntegrationFixtures::PRODUCT_ID),
            new Stock(99),
        );

        $row = $this->findById('product', IntegrationFixtures::PRODUCT_ID);
        $this->assertSame(99, (int) $row['stock']);
        $this->assertSame('Leche Entera', $row['name']); // other fields untouched
    }


    public function test_delete_removes_product_row(): void
    {
        $this->fixtures->loadProduct();

        $this->repository->delete(new Id(IntegrationFixtures::PRODUCT_ID));

        $this->assertFalse($this->findById('product', IntegrationFixtures::PRODUCT_ID));
    }

    public function test_delete_also_removes_price_history(): void
    {
        $this->repository->insert($this->buildProduct());

        $this->repository->delete(new Id(IntegrationFixtures::PRODUCT_ID));

        $this->assertSame(
            0,
            $this->countRows('price_history', ['idProduct' => IntegrationFixtures::PRODUCT_ID])
        );
    }


    public function test_search_all_returns_inserted_product(): void
    {
        $this->fixtures->loadProduct();

        $products = $this->searcher->searchAll();

        $ids = array_map(fn ($p) => $p->id()->value(), $products->items());
        $this->assertContains(IntegrationFixtures::PRODUCT_ID, $ids);
    }

    public function test_search_by_id_returns_correct_product(): void
    {
        $this->fixtures->loadProduct();

        $product = $this->searcher->search(new Id(IntegrationFixtures::PRODUCT_ID));

        $this->assertSame(IntegrationFixtures::PRODUCT_ID, $product->id()->value());
        $this->assertSame('Leche Entera', $product->name()->value());
    }

    public function test_search_by_id_throws_when_not_found(): void
    {
        $this->expectException(ItemNotFoundException::class);

        $this->searcher->search(new Id('c0000000-0000-0000-0000-000000000099'));
    }


    public function test_check_allocation_exists_throws_when_no_allocation(): void
    {
        $this->fixtures->loadProduct();

        $this->expectException(ItemNotFoundException::class);

        $this->repository->checkAllocationExists(new Id(IntegrationFixtures::PRODUCT_ID));
    }


    private function buildProduct(
        string $name  = 'Leche Entera',
        float  $price = IntegrationFixtures::PRODUCT_PRICE,
    ): Product {
        return Product::fromArray([
            'id'           => IntegrationFixtures::PRODUCT_ID,
            'name'         => $name,
            'price'        => $price,
            'ean'          => IntegrationFixtures::PRODUCT_EAN,
            'stock'        => IntegrationFixtures::PRODUCT_STOCK,
            'idTax'        => IntegrationFixtures::TAX_ID,
            'nameTax'      => 'IVA 21%',
            'percent'      => IntegrationFixtures::TAX_PCT,
            'idCategory'   => IntegrationFixtures::CATEGORY_ID,
            'nameCategory' => 'Lácteos',
            'idSupplier'   => IntegrationFixtures::SUPPLIER_ID,
            'nameSupplier' => 'Proveedor Test',
            'active'       => 1,
            'priceHistory' => [
                [
                    'id'        => 'b0000000-0000-0000-0000-000000000001',
                    'price'     => $price,
                    'startDate' => '2026-01-01',
                    'endDate'   => null,
                ],
            ],
            'image' => null,
        ]);
    }
}
