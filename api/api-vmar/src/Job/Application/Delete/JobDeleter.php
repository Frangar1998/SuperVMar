<?php

namespace SuperVMar\Job\Application\Delete;

use SuperVMar\Job\Domain\Exception\AllocationsFoundException;
use SuperVMar\Job\Domain\JobRepository;
use SuperVMar\Job\Domain\Service\JobSearcher;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class JobDeleter
{
    public function __construct(
        private JobSearcher $jobSearcher,
        private JobRepository $jobRepository,
    )
    {
    }

    public function delete(
        Id $id
    ): void
    {
        try {
            $this->jobSearcher->checkAllocations($id);
            throw new AllocationsFoundException();
        } catch (ItemNotFoundException) {
            $this->jobRepository->delete($id);
        }
    }
}