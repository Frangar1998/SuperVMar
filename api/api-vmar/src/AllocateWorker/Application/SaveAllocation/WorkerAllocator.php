<?php

namespace SuperVMar\AllocateWorker\Application\SaveAllocation;

use SuperVMar\AllocateWorker\Domain\Service\WorkerAllocationSearcher;
use SuperVMar\AllocateWorker\Domain\WorkerAllocation;
use SuperVMar\AllocateWorker\Domain\WorkerAllocationRepository;
use SuperVMar\AllocateWorker\Domain\WorkerAllocations;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class WorkerAllocator
{
    public function __construct(
        private WorkerAllocationRepository $workerAllocationRepository,
        private WorkerAllocationSearcher $workerAllocationSearcher
    )
    {
    }

    public function handleAllocations(
        Id $idUser,
        WorkerAllocations $newAllocations
    ): void
    {
        try {
            $workerAllocations = $this->workerAllocationSearcher->searchAll($idUser);
            foreach ($workerAllocations as $workerAllocation) {
                if ($newAllocations->find($workerAllocation) === null) {
                    $this->deallocate($workerAllocation);
                }
            }

            foreach ($newAllocations as $newAllocation) {
                $existingKey = $workerAllocations->find($newAllocation);
                if ($existingKey !== null) {
                    $existing = $workerAllocations->getItem($existingKey);
                    if (!$existing->idJob()->equals($newAllocation->idJob())) {
                        $this->updateAllocation($newAllocation);
                    }
                } else {
                    $this->allocate($newAllocation);
                }
            }

        } catch (ItemNotFoundException) {
            foreach ($newAllocations as $allocation) {
                $this->allocate($allocation);
            }
        }
    }

    protected function allocate(WorkerAllocation $allocation): void
    {
        $this->workerAllocationRepository->insert($allocation);
    }

    protected function updateAllocation(WorkerAllocation $allocation): void
    {
        $this->workerAllocationRepository->update($allocation);
    }

    protected function deallocate(WorkerAllocation $allocation): void
    {
        $this->workerAllocationRepository->delete($allocation);
    }
}