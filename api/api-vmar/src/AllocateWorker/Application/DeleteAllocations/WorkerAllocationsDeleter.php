<?php

namespace SuperVMar\AllocateWorker\Application\DeleteAllocations;

use SuperVMar\AllocateWorker\Domain\WorkerAllocationRepository;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class WorkerAllocationsDeleter
{
    public function __construct(
        private WorkerAllocationRepository $workerAllocationRepository,
    )
    {
    }

    public function deleteAllocationsFrom(Id $idUser): void
    {
        $this->workerAllocationRepository->deleteAll($idUser);
    }
}