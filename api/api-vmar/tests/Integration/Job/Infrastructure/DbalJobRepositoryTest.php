<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Integration\Job\Infrastructure;

use SuperVMar\App\Tests\Integration\Shared\Infrastructure\DbalTestCase;
use SuperVMar\Job\Domain\Job;
use SuperVMar\Job\Domain\JobRepository;
use SuperVMar\Job\Infrastructure\DbalJobRepository;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Criteria\FieldName;
use SuperVMar\Shared\Domain\Criteria\Filter;
use SuperVMar\Shared\Domain\Criteria\FilterField;
use SuperVMar\Shared\Domain\Criteria\FilterOperator;
use SuperVMar\Shared\Domain\Criteria\FilterValue;
use SuperVMar\Shared\Domain\Criteria\Filters;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;

final class DbalJobRepositoryTest extends DbalTestCase
{
    private const string JOB_ID   = 'b0000000-0000-0000-0000-000000000003';
    private const string JOB_NAME = 'Cajero';

    private JobRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(DbalJobRepository::class);
    }


    public function test_insert_persists_job_row(): void
    {
        $this->repository->insert($this->buildJob());

        $row = $this->findById('job', self::JOB_ID);
        $this->assertNotFalse($row);
        $this->assertSame(self::JOB_NAME, $row['name']);
    }

    public function test_insert_throws_on_duplicate_id(): void
    {
        $job = $this->buildJob();
        $this->repository->insert($job);

        $this->expectException(DuplicateItemException::class);
        $this->repository->insert($job);
    }


    public function test_update_changes_job_name(): void
    {
        $this->repository->insert($this->buildJob());
        $this->repository->update($this->buildJob(name: 'Repositor'));

        $row = $this->findById('job', self::JOB_ID);
        $this->assertSame('Repositor', $row['name']);
    }


    public function test_delete_removes_job_row(): void
    {
        $this->repository->insert($this->buildJob());
        $this->repository->delete(new Id(self::JOB_ID));

        $row = $this->findById('job', self::JOB_ID);
        $this->assertFalse($row);
    }


    public function test_search_returns_matching_job(): void
    {
        $this->repository->insert($this->buildJob());

        $criteria = new Criteria(new Filters([new Filter(
            new FilterField(TableNames::TABLE_JOB, new FieldName('id')),
            FilterOperator::EQUAL,
            new FilterValue(self::JOB_ID)
        )]));
        $jobs = $this->repository->searchByCriteria($criteria);

        $this->assertCount(1, $jobs);
    }

    public function test_search_throws_when_no_match(): void
    {
        $criteria = new Criteria(new Filters([new Filter(
            new FilterField(TableNames::TABLE_JOB, new FieldName('id')),
            FilterOperator::EQUAL,
            new FilterValue(self::JOB_ID)
        )]));

        $this->expectException(ItemNotFoundException::class);
        $this->repository->searchByCriteria($criteria);
    }


    private function buildJob(string $name = self::JOB_NAME): Job
    {
        return Job::fromArray(['id' => self::JOB_ID, 'name' => $name]);
    }
}
