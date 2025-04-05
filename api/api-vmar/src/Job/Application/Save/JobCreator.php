<?php

namespace SuperVMar\Job\Application\Save;

use SuperVMar\Job\Domain\Job;
use SuperVMar\Job\Domain\JobRepository;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;

final readonly class JobCreator
{
    public function __construct(
        private JobRepository $jobRepository,
    )
    {
    }

    public function create(
        Id      $id,
        Name    $name
    ): void
    {
        $this->jobRepository->insert(
            Job::create(
                $id,
                $name
            )
        );

    }
}