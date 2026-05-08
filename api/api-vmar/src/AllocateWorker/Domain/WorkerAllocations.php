<?php

namespace SuperVMar\AllocateWorker\Domain;

use SuperVMar\Shared\Domain\Collection;

final class WorkerAllocations extends Collection
{

    protected function type(): string
    {
        return WorkerAllocation::class;
    }

    public static function fromPrimitives(string $idUser, array $allocations): WorkerAllocations
    {
        return new self(
            array_map(
                fn(array $allocation) => WorkerAllocation::fromPrimitives($idUser, $allocation),
                $allocations
            )
        );
    }

    public static function fromArray(array $allocations): WorkerAllocations
    {
        return new self(
            array_map(
                fn(array $allocation) => WorkerAllocation::fromArray($allocation),
                $allocations
            )
        );
    }
}