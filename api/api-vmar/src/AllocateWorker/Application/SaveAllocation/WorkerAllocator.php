<?php

namespace SuperVMar\AllocateWorker\Application\SaveAllocation;

use SuperVMar\AllocateWorker\Domain\Service\WorkerAllocationSearcher;
use SuperVMar\AllocateWorker\Domain\WorkerAllocation;
use SuperVMar\AllocateWorker\Domain\WorkerAllocationRepository;
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

    public function allocate(
        Id $idUser,
        Id $idSupermarket,
        Id $idJob
    ): void
    {
        try {
            $workerAllocation = $this->workerAllocationSearcher->search($idUser, $idSupermarket);
            $workerAllocation->changeJob($idJob);

            $this->workerAllocationRepository->update($workerAllocation);

        } catch (ItemNotFoundException) {
            $this->workerAllocationRepository->insert(
                new WorkerAllocation(
                    $idUser,
                    $idSupermarket,
                    $idJob
                )
            );
        }
    }
}