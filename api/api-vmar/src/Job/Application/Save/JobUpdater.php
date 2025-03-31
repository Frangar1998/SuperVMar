<?php

namespace SuperVMar\Job\Application\Save;

use SuperVMar\Job\Domain\JobRepository;
use SuperVMar\Job\Domain\Service\JobSearcher;
use SuperVMar\Job\Domain\ValueObject\Name;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class JobUpdater
{
    public function __construct(
        private JobSearcher $jobSearcher,
        private JobRepository $jobRepository
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function update(
        Id      $id,
        Name    $name
    ): void
    {
        $job = $this->jobSearcher->search($id);
        $job->changeName($name);
        $this->jobRepository->update($job);
    }
}