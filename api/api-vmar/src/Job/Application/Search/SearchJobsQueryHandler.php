<?php

namespace SuperVMar\Job\Application\Search;

use SuperVMar\Job\Domain\Service\JobSearcher;
use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;

final readonly class SearchJobsQueryHandler implements QueryHandler
{
    public function __construct(
        private JobSearcher $jobSearcher
    )
    {
    }

    public function __invoke(SearchJobsQuery $query): JobsResponse
    {

        $jobs = $this->jobSearcher->searchAll();

        return new JobsResponse(
            $jobs->toArray(),
        );
    }
}