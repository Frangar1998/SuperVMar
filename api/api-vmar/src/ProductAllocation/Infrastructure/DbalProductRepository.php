<?php

namespace SuperVMar\ProductAllocation\Infrastructure;

use Doctrine\DBAL\Connection;
use SuperVMar\ProductAllocation\Domain\Entity\Product;
use SuperVMar\ProductAllocation\Domain\ProductRepository;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use Throwable;

final readonly class DbalProductRepository implements ProductRepository
{
    private const string TABLE_PRODUCT = TableNames::TABLE_PRODUCT->value;

    public function __construct(
        private Connection $connection,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     * @throws InternalErrorException
     */
    public function searchById(Id $idProduct): Product
    {
        try {
            $result = $this->connection->createQueryBuilder()
                ->select('id', 'name', 'stock', 'image')
                ->from(self::TABLE_PRODUCT)
                ->where('id = :id')
                ->setParameter('id', $idProduct)
                ->executeQuery()
                ->fetchAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$result) {
            throw new ItemNotFoundException(Product::class, ['id' => $idProduct->value()]);
        }

        return Product::fromArray($result);
    }
}

