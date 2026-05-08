<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Integration\ProductAllocation\Infrastructure;

use SuperVMar\App\Tests\Fixtures\IntegrationFixtures;
use SuperVMar\App\Tests\Integration\Shared\Infrastructure\DbalTestCase;
use SuperVMar\ProductAllocation\Domain\Entity\Product as AllocationProduct;
use SuperVMar\ProductAllocation\Domain\Entity\Space;
use SuperVMar\ProductAllocation\Domain\ProductAllocation;
use SuperVMar\ProductAllocation\Domain\ProductAllocationRepository;
use SuperVMar\ProductAllocation\Domain\ValueObject\Quantity;
use SuperVMar\ProductAllocation\Infrastructure\DbalProductAllocationRepository;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\ValueObject\Id;

final class DbalProductAllocationRepositoryTest extends DbalTestCase
{
    private const string ZONE_ID  = 'b0000000-0000-0000-0000-000000000050';
    private const string SPACE_ID = 'b0000000-0000-0000-0000-000000000051';

    private ProductAllocationRepository $repository;
    private IntegrationFixtures $fixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(DbalProductAllocationRepository::class);
        $this->fixtures   = new IntegrationFixtures($this->connection);

        $this->fixtures->loadCatalog();
        $this->fixtures->loadProduct();

        $this->fixtures->loadUser();

        $this->connection->insert('zone', [
            'id'               => self::ZONE_ID,
            'name'             => 'Zona A',
            'idSupermarket'    => IntegrationFixtures::SUPERMARKET_ID,
            'cornerTopLeft'    => '{"x":0,"y":0}',
            'cornerTopRight'   => '{"x":10,"y":0}',
            'cornerBottomRight'=> '{"x":10,"y":10}',
            'cornerBottomLeft' => '{"x":0,"y":10}',
        ]);

        $this->connection->insert('space', [
            'id'       => self::SPACE_ID,
            'position' => '{"x":1,"y":1}',
            'idZone'   => self::ZONE_ID,
            'maxSpots' => 5,
        ]);
    }


    public function test_insert_persists_allocation_row(): void
    {
        $this->repository->insert($this->buildAllocation());

        $count = $this->countRows('product_allocation', ['idProduct' => IntegrationFixtures::PRODUCT_ID]);
        $this->assertSame(1, $count);
    }

    public function test_insert_throws_on_duplicate_space(): void
    {
        $this->repository->insert($this->buildAllocation());

        $this->expectException(DuplicateItemException::class);
        $this->repository->insert($this->buildAllocation());
    }


    public function test_update_changes_quantity(): void
    {
        $this->repository->insert($this->buildAllocation(quantity: 3));
        $this->repository->update($this->buildAllocation(quantity: 7));

        $row = $this->fetchAllocation();
        $this->assertSame(7, (int) $row['quantity']);
    }


    public function test_update_quantity_only(): void
    {
        $this->repository->insert($this->buildAllocation(quantity: 5));
        $this->repository->updateQuantity($this->buildAllocation(quantity: 2));

        $row = $this->fetchAllocation();
        $this->assertSame(2, (int) $row['quantity']);
    }


    public function test_delete_removes_allocation(): void
    {
        $this->repository->insert($this->buildAllocation());
        $this->repository->delete(new Id(self::SPACE_ID));

        $count = $this->countRows('product_allocation', ['idSpace' => self::SPACE_ID]);
        $this->assertSame(0, $count);
    }


    private function buildAllocation(int $quantity = 10): ProductAllocation
    {
        $space = Space::fromArray([
            'id'       => self::SPACE_ID,
            'position' => ['x' => 1, 'y' => 1],
            'maxSpots' => 5,
            'zone'     => [
                'id'                => self::ZONE_ID,
                'name'              => 'Zona A',
                'cornerTopLeft'     => ['x' => 0, 'y' => 0],
                'cornerTopRight'    => ['x' => 10, 'y' => 0],
                'cornerBottomLeft'  => ['x' => 0, 'y' => 10],
                'cornerBottomRight' => ['x' => 10, 'y' => 10],
            ],
        ]);

        $product = AllocationProduct::fromArray([
            'id'    => IntegrationFixtures::PRODUCT_ID,
            'name'  => 'Leche Entera',
            'stock' => 50,
            'image' => null,
        ]);

        return new ProductAllocation($product, $space, new Quantity($quantity));
    }

    private function fetchAllocation(): array|false
    {
        return $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from('product_allocation')
            ->where('idSpace = :idSpace')
            ->setParameter('idSpace', self::SPACE_ID)
            ->executeQuery()
            ->fetchAssociative();
    }
}
