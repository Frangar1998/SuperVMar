<?php

namespace SuperVMar\AllocateWorker\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
use SuperVMar\AllocateWorker\Domain\WorkerAllocation;
use SuperVMar\AllocateWorker\Domain\WorkerAllocationRepository;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Infrastructure\Doctrine\DbalCriteriaConverter;
use Throwable;

final readonly class DbalWorkerAllocationRepository implements WorkerAllocationRepository
{
    private const string TABLE_WORKER_ALLOCATION = TableNames::TABLE_WORKER_ALLOCATION->value;

    public function __construct(
        private Connection $connection,
        private DbalCriteriaConverter $dbalCriteriaConverter,
    )
    {
    }

    /**
     * @throws DuplicateItemException
     * @throws InternalErrorException
     */
    public function insert(WorkerAllocation $workerAllocation): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->insert(self::TABLE_WORKER_ALLOCATION)
                ->values(
                    [
                        'idUser' => ':idUser',
                        'idSupermarket' => ':idSupermarket',
                        'idUserJob' => ':idUserJob',
                    ])
                ->setParameters(
                    [
                        'idUser' => $workerAllocation->idUser(),
                        'idSupermarket' => $workerAllocation->idSupermarket(),
                        'idUserJob' => $workerAllocation->idJob(),
                    ])
                ->executeStatement();

        } catch (UniqueConstraintViolationException) {
            throw new DuplicateItemException(
                WorkerAllocation::class,
                sprintf("user:<%s> supermarket: <%s>", $workerAllocation->idUser(), $workerAllocation->idSupermarket())
            );
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function update(WorkerAllocation $workerAllocation): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->update(self::TABLE_WORKER_ALLOCATION)
                ->set('idUserJob', ':idUserJob')
                ->where('idUser = :idUser')
                ->andWhere('idSupermarket = :idSupermarket')
                ->setParameters(
                    [
                        'idUser' => $workerAllocation->idUser(),
                        'idSupermarket' => $workerAllocation->idSupermarket(),
                        'idUserJob' => $workerAllocation->idJob(),
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function delete(Id $userId, Id $supermarketId): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_WORKER_ALLOCATION)
                ->where('idUser = :idUser')
                ->andWhere('idSupermarket = :idSupermarket')
                ->setParameters(
                    [
                        'idUser' => $userId,
                        'idSupermarket' => $supermarketId,
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): WorkerAllocation
    {
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $workerAllocation = $query->executeQuery()->fetchAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$workerAllocation) {
            throw new ItemNotFoundException($workerAllocation::class, $criteria->filters()->items());
        }

        return WorkerAllocation::fromArray($workerAllocation);
    }

    private function buildQueryByCriteria(Criteria $criteria): QueryBuilder
    {
        return $this->dbalCriteriaConverter->convert(
            self::TABLE_WORKER_ALLOCATION,
            $criteria,
            $this->connection->createQueryBuilder()
        );
    }
}