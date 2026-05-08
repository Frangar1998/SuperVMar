<?php

namespace SuperVMar\AllocateWorker\Domain;

use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

interface WorkerAllocationRepository
{
    public function insert(WorkerAllocation $workerAllocation): void;
    public function update(WorkerAllocation $workerAllocation): void;
    public function delete(WorkerAllocation $workerAllocation): void;
    public function deleteAll(Id $idUser): void;
    /**
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): WorkerAllocations;
}