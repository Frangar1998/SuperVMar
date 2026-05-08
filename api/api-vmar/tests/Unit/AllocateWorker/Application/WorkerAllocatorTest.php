<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\AllocateWorker\Application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SuperVMar\AllocateWorker\Application\SaveAllocation\WorkerAllocator;
use SuperVMar\AllocateWorker\Domain\Service\WorkerAllocationSearcher;
use SuperVMar\AllocateWorker\Domain\WorkerAllocation;
use SuperVMar\AllocateWorker\Domain\WorkerAllocationRepository;
use SuperVMar\AllocateWorker\Domain\WorkerAllocations;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

final class WorkerAllocatorTest extends TestCase
{
    private const string USER_ID         = '550e8400-e29b-41d4-a716-446655440001';
    private const string SUPERMARKET_A   = '550e8400-e29b-41d4-a716-446655440010';
    private const string SUPERMARKET_B   = '550e8400-e29b-41d4-a716-446655440011';
    private const string JOB_OLD         = '550e8400-e29b-41d4-a716-446655440020';
    private const string JOB_NEW         = '550e8400-e29b-41d4-a716-446655440021';

    private WorkerAllocationRepository&MockObject $repository;
    private WorkerAllocationSearcher&MockObject   $searcher;
    private WorkerAllocator $allocator;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(WorkerAllocationRepository::class);
        $this->searcher   = $this->createMock(WorkerAllocationSearcher::class);
        $this->allocator  = new WorkerAllocator($this->repository, $this->searcher);
    }


    public function test_when_no_existing_allocations_inserts_all_new(): void
    {
        $this->searcher->expects($this->once())
            ->method('searchAll')
            ->willThrowException(new ItemNotFoundException(WorkerAllocation::class, ['idUser' => self::USER_ID]));

        $allocation = WorkerAllocation::fromArray([
            'idUser'        => self::USER_ID,
            'idSupermarket' => self::SUPERMARKET_A,
            'idJob'         => self::JOB_OLD,
        ]);

        $this->repository->expects($this->once())
            ->method('insert')
            ->with($allocation);

        $this->allocator->handleAllocations(
            new Id(self::USER_ID),
            WorkerAllocations::fromArray([
                ['idUser' => self::USER_ID, 'idSupermarket' => self::SUPERMARKET_A, 'idJob' => self::JOB_OLD],
            ])
        );
    }


    public function test_allocates_new_supermarket_not_in_existing(): void
    {
        $existingAllocation = WorkerAllocation::fromArray([
            'idUser'        => self::USER_ID,
            'idSupermarket' => self::SUPERMARKET_A,
            'idJob'         => self::JOB_OLD,
        ]);

        $this->searcher->expects($this->once())
            ->method('searchAll')
            ->willReturn(new WorkerAllocations([$existingAllocation]));

        $newAllocation = WorkerAllocation::fromArray([
            'idUser'        => self::USER_ID,
            'idSupermarket' => self::SUPERMARKET_B,
            'idJob'         => self::JOB_NEW,
        ]);

        $this->repository->expects($this->never())->method('delete');
        $this->repository->expects($this->never())->method('update');
        $this->repository->expects($this->once())->method('insert')->with($newAllocation);

        $this->allocator->handleAllocations(
            new Id(self::USER_ID),
            WorkerAllocations::fromArray([
                ['idUser' => self::USER_ID, 'idSupermarket' => self::SUPERMARKET_A, 'idJob' => self::JOB_OLD],
                ['idUser' => self::USER_ID, 'idSupermarket' => self::SUPERMARKET_B, 'idJob' => self::JOB_NEW],
            ])
        );
    }


    public function test_deallocates_supermarket_removed_from_new(): void
    {
        $existingA = WorkerAllocation::fromArray([
            'idUser'        => self::USER_ID,
            'idSupermarket' => self::SUPERMARKET_A,
            'idJob'         => self::JOB_OLD,
        ]);
        $existingB = WorkerAllocation::fromArray([
            'idUser'        => self::USER_ID,
            'idSupermarket' => self::SUPERMARKET_B,
            'idJob'         => self::JOB_OLD,
        ]);

        $this->searcher->expects($this->once())
            ->method('searchAll')
            ->willReturn(new WorkerAllocations([$existingA, $existingB]));

        $this->repository->expects($this->once())->method('delete')->with($existingB);
        $this->repository->expects($this->never())->method('update');
        $this->repository->expects($this->never())->method('insert');

        $this->allocator->handleAllocations(
            new Id(self::USER_ID),
            WorkerAllocations::fromArray([
                ['idUser' => self::USER_ID, 'idSupermarket' => self::SUPERMARKET_A, 'idJob' => self::JOB_OLD],
            ])
        );
    }


    public function test_updates_existing_allocation_when_same_supermarket(): void
    {
        $existing = WorkerAllocation::fromArray([
            'idUser'        => self::USER_ID,
            'idSupermarket' => self::SUPERMARKET_A,
            'idJob'         => self::JOB_OLD,
        ]);

        $this->searcher->expects($this->once())
            ->method('searchAll')
            ->willReturn(new WorkerAllocations([$existing]));

        $this->repository->expects($this->never())->method('insert');
        $this->repository->expects($this->never())->method('delete');
        $this->repository->expects($this->once())->method('update');

        $this->allocator->handleAllocations(
            new Id(self::USER_ID),
            WorkerAllocations::fromArray([
                ['idUser' => self::USER_ID, 'idSupermarket' => self::SUPERMARKET_A, 'idJob' => self::JOB_NEW],
            ])
        );
    }


    public function test_removes_all_when_new_allocations_empty(): void
    {
        $existing = WorkerAllocation::fromArray([
            'idUser'        => self::USER_ID,
            'idSupermarket' => self::SUPERMARKET_A,
            'idJob'         => self::JOB_OLD,
        ]);

        $this->searcher->expects($this->once())
            ->method('searchAll')
            ->willReturn(new WorkerAllocations([$existing]));

        $this->repository->expects($this->once())->method('delete')->with($existing);
        $this->repository->expects($this->never())->method('insert');
        $this->repository->expects($this->never())->method('update');

        $this->allocator->handleAllocations(
            new Id(self::USER_ID),
            new WorkerAllocations([])
        );
    }
}
