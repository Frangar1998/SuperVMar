<?php

namespace SuperVMar\ProductAllocation\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
use SuperVMar\ProductAllocation\Domain\ProductAllocation;
use SuperVMar\ProductAllocation\Domain\ProductAllocationRepository;
use SuperVMar\ProductAllocation\Domain\ProductsAllocations;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Infrastructure\Doctrine\DbalCriteriaConverter;
use Throwable;

final readonly class DbalProductAllocationRepository implements ProductAllocationRepository
{
    private const string TABLE_PRODUCT_ALLOCATION = TableNames::TABLE_PRODUCT_ALLOCATION->value;

    public function __construct(
        private Connection $connection,
        private DbalCriteriaConverter $dbalCriteriaConverter
    )
    {
    }

    /**
     * @throws DuplicateItemException
     * @throws InternalErrorException
     */public function insert(ProductAllocation $productAllocation): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->insert(self::TABLE_PRODUCT_ALLOCATION)
                ->values(
                    [
                        'idProduct' => ':idProduct',
                        'idSpace' => ':idSpace',
                        'quantity' => ':quantity',
                    ])
                ->setParameters(
                    [
                        'idProduct' => $productAllocation->product()->id(),
                        'idSpace' => $productAllocation->space()->id(),
                        'quantity' => $productAllocation->quantity(),
                    ])
                ->executeStatement();

        } catch (UniqueConstraintViolationException) {
            throw new DuplicateItemException(ProductAllocation::class, $productAllocation->id());
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */public function update(ProductAllocation $productAllocation): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->update(self::TABLE_PRODUCT_ALLOCATION)
                ->set('idProduct', ':idProduct')
                ->set('quantity', ':quantity')
                ->where('idSpace = :idSpace')
                ->setParameters(
                    [
                        'idSpace' => $productAllocation->space()->id(),
                        'idProduct' => $productAllocation->product()->id(),
                        'quantity' => $productAllocation->quantity(),
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */public function updateQuantity(ProductAllocation $productAllocation): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->update(self::TABLE_PRODUCT_ALLOCATION)
                ->set('quantity', ':quantity')
                ->where('idProduct = :idProduct')
                ->setParameters(
                    [
                        'idProduct' => $productAllocation->product()->id(),
                        'quantity' => $productAllocation->quantity(),
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */public function delete(Id $idSpace): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_PRODUCT_ALLOCATION)
                ->where('idSpace = :idSpace')
                ->setParameters(
                    [
                        'idSpace' => $idSpace,
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     * @throws ItemNotFoundException
     */public function searchByCriteria(Criteria $criteria): ProductsAllocations
    {
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $productsAllocations = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$productsAllocations) {
            throw new ItemNotFoundException(ProductAllocation::class, $criteria->filters()?->toArray());
        }

        return ProductsAllocations::fromArray($productsAllocations);
    }

    private function buildQueryByCriteria(Criteria $criteria): QueryBuilder
    {
        return $this->dbalCriteriaConverter->convert(
            self::TABLE_PRODUCT_ALLOCATION,
            $criteria,
            $this->connection->createQueryBuilder()
        );
    }
}