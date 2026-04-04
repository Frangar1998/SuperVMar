<?php

namespace SuperVMar\Product\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
use SuperVMar\Product\Domain\Product;
use SuperVMar\Product\Domain\ProductRepository;
use SuperVMar\Product\Domain\Products;
use SuperVMar\Product\Domain\ValueObject\Stock;
use SuperVMar\Product\Infrastructure\Dao\DbalPriceHistoryDao;
use SuperVMar\Product\Infrastructure\Dao\DbalProductAllocationDao;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Infrastructure\Doctrine\DbalCriteriaConverter;
use Throwable;

final readonly class DbalProductRepository implements ProductRepository
{
    private const string TABLE_PRODUCT = TableNames::TABLE_PRODUCT->value;

    public function __construct(
        private Connection $connection,
        private DbalCriteriaConverter $dbalCriteriaConverter,
        private DbalPriceHistoryDao $dbalPriceHistoryDao,
        private DbalProductAllocationDao $dbalProductAllocationDao,
    )
    {
    }

    /**
     * @throws DuplicateItemException
     * @throws InternalErrorException
     */
    public function insert(Product $product): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->insert(self::TABLE_PRODUCT)
                ->values(
                    [
                        'id' => ':id',
                        'name' => ':name',
                        'price' => ':price',
                        'ean' => ':ean',
                        'stock' => ':stock',
                        'image' => ':image',
                        'idTax' => ':idTax',
                        'idCategory' => ':idCategory',
                        'idSupplier' => ':idSupplier',
                        'active' => ':active',
                    ])
                ->setParameters(
                    [
                        'id' => $product->id(),
                        'name' => $product->name(),
                        'price' => $product->price(),
                        'ean' => $product->ean(),
                        'stock' => $product->stock(),
                        'image' => $product->image()?->value(),
                        'idTax' => $product->tax()->id(),
                        'idCategory' => $product->category()->id(),
                        'idSupplier' => $product->supplier()->id(),
                        'active' => $product->active(),
                    ])
                ->executeStatement();

            $this->dbalPriceHistoryDao->insert($product->priceHistory(), $product->id());

        } catch (UniqueConstraintViolationException) {
            throw new DuplicateItemException(Product::class, $product->id());
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function update(Product $product): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->update(self::TABLE_PRODUCT)
                ->set('name', ':name')
                ->set('price', ':price')
                ->set('stock', ':stock')
                ->set('image', ':image')
                ->set('idTax', ':idTax')
                ->set('idCategory', ':idCategory')
                ->set('active', ':active')
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $product->id(),
                        'name' => $product->name(),
                        'price' => $product->price(),
                        'stock' => $product->stock(),
                        'image' => $product->image()?->value(),
                        'idTax' => $product->tax()->id(),
                        'idCategory' => $product->category()->id(),
                        'active' => $product->active(),
                    ])
                ->executeStatement();

            $this->dbalPriceHistoryDao->update($product->priceHistory(), $product->id());

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function updateStock(Id $idProduct, Stock $stock): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->update(self::TABLE_PRODUCT)
                ->set('stock', ':stock')
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $idProduct,
                        'stock' => $stock,
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function delete(Id $idProduct): void
    {
        try {
            $this->dbalPriceHistoryDao->deleteAll($idProduct);

            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_PRODUCT)
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $idProduct,
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws ItemNotFoundException
     * @throws InternalErrorException
     */
    public function checkAllocationExists(Id $idProduct): void
    {
        $this->dbalProductAllocationDao->checkAllocationExists($idProduct);
    }

    /**
     * @throws InternalErrorException
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): Products
    {
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $products = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$products) {
            throw new ItemNotFoundException(Product::class, $criteria->filters()?->toArray() ?? []);
        }

        foreach ($products as $key => $product) {
            $products[$key]['priceHistory'] = $this->dbalPriceHistoryDao->search(new Id($product['id']));
        }

        return Products::fromArray($products);
    }

    private function buildQueryByCriteria(Criteria $criteria): QueryBuilder
    {
        return $this->dbalCriteriaConverter->convert(
            self::TABLE_PRODUCT,
            $criteria,
            $this->connection->createQueryBuilder()
        );
    }
}