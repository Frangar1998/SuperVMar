<?php

namespace SuperVMar\AllocateWorker\Domain\Service;

use SuperVMar\AllocateWorker\Domain\WorkerAllocation;
use SuperVMar\AllocateWorker\Domain\WorkerAllocationRepository;
use SuperVMar\AllocateWorker\Domain\WorkerAllocations;
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

readonly class WorkerAllocationSearcher
{
    public function __construct(
        private WorkerAllocationRepository $workerAllocationRepository,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function search(Id $idUser, Id $idSupermarket): WorkerAllocation
    {
        return $this->workerAllocationRepository->searchByCriteria(
            new Criteria(
                filters: new Filters(
                    [
                        new Filter(
                            new FilterField(TableNames::TABLE_WORKER_ALLOCATION, new FieldName('idUser')),
                            FilterOperator::EQUAL,
                            new FilterValue($idUser)
                        ),
                        new Filter(
                            new FilterField(TableNames::TABLE_WORKER_ALLOCATION, new FieldName('idSupermarket')),
                            FilterOperator::EQUAL,
                            new FilterValue($idSupermarket)
                        )
                    ]
                )
            )
        )->first();
    }

    /**
     * @throws ItemNotFoundException
     */
    public function searchAll(Id $idUser): WorkerAllocations
    {
        return $this->workerAllocationRepository->searchByCriteria(
            new Criteria(
                filters: new Filters(
                    [
                        new Filter(
                            new FilterField(TableNames::TABLE_WORKER_ALLOCATION, new FieldName('idUser')),
                            FilterOperator::EQUAL,
                            new FilterValue($idUser)
                        )
                    ]
                )
            )
        );
    }
}