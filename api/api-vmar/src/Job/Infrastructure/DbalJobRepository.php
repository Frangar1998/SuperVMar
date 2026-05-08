<?php

namespace SuperVMar\Job\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
use SuperVMar\Job\Domain\Job;
use SuperVMar\Job\Domain\JobRepository;
use SuperVMar\Job\Domain\Jobs;
use SuperVMar\Job\Infrastructure\Dao\DbalWorkerAllocationDao;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Infrastructure\Doctrine\DbalCriteriaConverter;
use Throwable;

final readonly class DbalJobRepository implements JobRepository
{
    private const string TABLE_JOB = TableNames::TABLE_JOB->value;

    public function __construct(
        private Connection $connection,
        private DbalCriteriaConverter $dbalCriteriaConverter,
        private DbalWorkerAllocationDao  $workerAllocationDao
    )
    {
    }

    /**
     * @throws DuplicateItemException
     * @throws InternalErrorException
     */
    public function insert(Job $job): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->insert(self::TABLE_JOB)
                ->values(
                    [
                        'id' => ':id',
                        'name' => ':name',
                    ])
                ->setParameters(
                    [
                        'id' => $job->id(),
                        'name' => $job->name(),
                    ])
                ->executeStatement();

        } catch (UniqueConstraintViolationException) {
            throw new DuplicateItemException(Job::class, $job->id());
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function update(Job $job): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->update(self::TABLE_JOB)
                ->set('name', ':name')
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $job->id(),
                        'name' => $job->name(),
                    ])
                ->executeStatement();

        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function delete(Id $idJob): void
    {
        try {
            $this->connection->createQueryBuilder()
                ->delete(self::TABLE_JOB)
                ->where('id = :id')
                ->setParameters(
                    [
                        'id' => $idJob,
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
    public function searchByCriteria(Criteria $criteria): Jobs
    {
        try {
            $query = $this->buildQueryByCriteria($criteria);
            $jobs = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new InternalErrorException($e->getMessage(), $e);
        }

        if (!$jobs) {
            throw new ItemNotFoundException(Job::class, $criteria->filters()?->toArray() ?? []);
        }

        return Jobs::fromArray($jobs);
    }

    /**
     * @throws ItemNotFoundException
     * @throws InternalErrorException
     */
    public function checkAllocationsExists(Id $idJob): void
    {
        $this->workerAllocationDao->checkAllocationsExists($idJob);
    }

    private function buildQueryByCriteria(Criteria $criteria): QueryBuilder
    {
        return $this->dbalCriteriaConverter->convert(
            self::TABLE_JOB,
            $criteria,
            $this->connection->createQueryBuilder()
        );
    }
}