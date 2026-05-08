<?php

namespace SuperVMar\Job\Application\Search;

use SuperVMar\Shared\Domain\Bus\Query\Response;

final readonly class JobsResponse implements Response
{
    public function __construct(
        private array $jobs,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'jobs' => $this->jobs,
        ];
    }
}