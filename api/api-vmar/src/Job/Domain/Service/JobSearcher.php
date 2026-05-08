<?php

namespace SuperVMar\Job\Domain\Service;

use SuperVMar\Job\Domain\Job;
use SuperVMar\Job\Domain\JobRepository;
use SuperVMar\Job\Domain\Jobs;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Criteria\FieldName;
use SuperVMar\Shared\Domain\Criteria\Filter;
use SuperVMar\Shared\Domain\Criteria\FilterField;
use SuperVMar\Shared\Domain\Criteria\FilterOperator;
use SuperVMar\Shared\Domain\Criteria\Filters;
use SuperVMar\Shared\Domain\Criteria\FilterValue;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;

readonly class JobSearcher
{
    public function __construct(
        private JobRepository $jobRepository,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function search(Id $idJob): Job
    {
        return $this->jobRepository->searchByCriteria(
            new Criteria(
                filters: new Filters(
                    [
                        new Filter(
                            new FilterField(TableNames::TABLE_JOB, new FieldName('id')),
                            FilterOperator::EQUAL,
                            new FilterValue($idJob)
                        )
                    ]
                )
            )
        )->first();
    }

    /**
     * @throws ItemNotFoundException
     */
    public function searchAll(): Jobs
    {
        return $this->jobRepository->searchByCriteria(
            new Criteria()
        );
    }

    /**
     * @throws ItemNotFoundException
     */
    public function checkAllocations(Id $idJob): void
    {
        $this->jobRepository->checkAllocationsExists($idJob);
    }
}