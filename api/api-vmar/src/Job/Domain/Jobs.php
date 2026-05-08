<?php

namespace SuperVMar\Job\Domain;

use SuperVMar\Shared\Domain\Collection;

final class Jobs extends Collection
{

    protected function type(): string
    {
        return Job::class;
    }

    public static function fromArray(array $jobs): self
    {
        return new self(
            array_map(
                fn(array $job) => Job::fromArray($job),
                $jobs
            )
        );
    }
}