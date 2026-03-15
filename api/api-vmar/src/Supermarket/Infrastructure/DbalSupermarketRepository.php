<?php

namespace SuperVMar\Supermarket\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Infrastructure\Doctrine\DbalCriteriaConverter;
use SuperVMar\Supermarket\Domain\Supermarket;
use SuperVMar\Supermarket\Domain\Supermarkets;
use SuperVMar\Supermarket\Domain\SupermarketRepository;
use SuperVMar\Supermarket\Infrastructure\Dao\DbalAddressDao;
use SuperVMar\Supermarket\Infrastructure\Dao\DbalZoneDao;
use Throwable;

final readonly class DbalSupermarketRepository implements SupermarketRepository
{
    private const string TABLE_SUPERMARKET = TableNames::TABLE_SUPERMARKET->value;

    public function __construct(
        private Connection $connection,
        private DbalCriteriaConverter $dbalCriteriaConverter,
        private DbalAddressDao $addressDao,
        private DbalZoneDao $zoneDao
    )
    {
    }

    /**
     * @throws DuplicateItemException
     * @throws InternalErrorException
     */
    public function insert(Supermarket $supermarket): void
    {
        try {
            $this->addressDao->insert($supermarket->address());

            $this->connection->createQueryBuilder()
                ->insert(self::TABLE_SUPERMARKET)
                ->values(
                    [
                        'id' => ':id',
                        'name' => ':name',
                        'phone' => ':phone',
                        'email' => ':email',
                        'idAddress' => ':idAddress',
                    ])
                ->setParameters(
                    [
                        'id' => $supermarket->id(),
                        'name' => $supermarket->name(),
                        'phone' => $supermarket->phone(),
                        'email' => $supermarket->email(),
                        'idAddress' => $supermarket->address()->id(),
                    ])
                ->executeStatement();

            $this->zoneDao->insert($supermarket->zones(), $supermarket->id());
        } catch (UniqueConstraintViolationException) {
            throw new DuplicateItemException(Supermarket::class, $supermarket->id());
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function update(Supermarket $supermarket): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->update(self::TABLE_SUPERMARKET)
                ->set('name', ':name')
                ->set('phone', ':phone')
                ->set('email', ':email')
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $supermarket->id(),
                        'name' => $supermarket->name(),
                        'phone' => $supermarket->phone(),
                        'email' => $supermarket->email(),
                    ])
                ->executeStatement();

            $this->addressDao->update($supermarket->address());
            $this->zoneDao->update($supermarket->zones(), $supermarket->id());
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function delete(Supermarket $supermarket): void
    {
        try {
            $this->zoneDao->deleteAll($supermarket->zones(), $supermarket->id());

            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_SUPERMARKET)
                ->where('id = :id')
                ->setParameter('id', $supermarket->id())
                ->executeStatement();

            $this->addressDao->delete($supermarket->address()->id());

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws ItemNotFoundException
     * @throws InternalErrorException
     */
    public function searchByCriteria(Criteria $criteria): ?Supermarket
    {
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $supermarket = $query->executeQuery()->fetchAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$supermarket) {
            throw new ItemNotFoundException(Supermarket::class, $criteria->filters()->toArray());
        }

        $supermarket['zones'] = $this->zoneDao->search(new Id($supermarket['id']));

        return Supermarket::fromArray($supermarket);
    }

    /**
     * @throws ItemNotFoundException
     * @throws InternalErrorException
     */
    public function searchAllByCriteria(Criteria $criteria): Supermarkets
    {
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $supermarkets = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$supermarkets) {
            throw new ItemNotFoundException(Supermarket::class, $criteria->filters()?->toArray());
        }

        foreach ($supermarkets as $key => $supermarket) {
            $supermarkets[$key]['zones'] = $this->zoneDao->search(new Id($supermarket['id']));
        }

        return Supermarkets::fromArray($supermarkets);
    }

    private function buildQueryByCriteria(Criteria $criteria): QueryBuilder
    {
        return $this->dbalCriteriaConverter->convert(
            self::TABLE_SUPERMARKET,
            $criteria,
            $this->connection->createQueryBuilder()
        );
    }
}