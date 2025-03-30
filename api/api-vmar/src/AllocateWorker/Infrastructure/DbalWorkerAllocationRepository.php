<?php

namespace SuperVMar\AllocateWorker\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
use SuperVMar\AllocateWorker\Domain\WorkerAllocation;
use SuperVMar\AllocateWorker\Domain\WorkerAllocationRepository;
use SuperVMar\AllocateWorker\Domain\WorkerAllocations;
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
                        'idJob' => ':idJob',
                    ])
                ->setParameters(
                    [
                        'idUser' => $workerAllocation->idUser(),
                        'idSupermarket' => $workerAllocation->idSupermarket(),
                        'idJob' => $workerAllocation->idJob(),
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
                ->set('idJob', ':idJob')
                ->where('idUser = :idUser')
                ->andWhere('idSupermarket = :idSupermarket')
                ->setParameters(
                    [
                        'idUser' => $workerAllocation->idUser(),
                        'idSupermarket' => $workerAllocation->idSupermarket(),
                        'idJob' => $workerAllocation->idJob(),
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function delete(WorkerAllocation $workerAllocation): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_WORKER_ALLOCATION)
                ->where('idUser = :idUser')
                ->andWhere('idSupermarket = :idSupermarket')
                ->setParameters(
                    [
                        'idUser' => $workerAllocation->idUser(),
                        'idSupermarket' => $workerAllocation->idSupermarket(),
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function deleteAll(Id $idUser): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_WORKER_ALLOCATION)
                ->where('idUser = :idUser')
                ->setParameters(
                    [
                        'idUser' => $idUser,
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
    public function searchByCriteria(Criteria $criteria): WorkerAllocations
    {
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $workerAllocations = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$workerAllocations) {
            throw new ItemNotFoundException(WorkerAllocation::class, $criteria->filters()->toArray());
        }

        return WorkerAllocations::fromArray($workerAllocations);
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