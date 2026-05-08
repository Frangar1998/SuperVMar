<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Job\Application;

use PHPUnit\Framework\TestCase;
use SuperVMar\Job\Application\Search\JobsResponse;
use SuperVMar\Job\Application\Search\SearchJobsQuery;
use SuperVMar\Job\Application\Search\SearchJobsQueryHandler;
use SuperVMar\Job\Domain\Jobs;
use SuperVMar\Job\Domain\Service\JobSearcher;

final class SearchJobsQueryHandlerTest extends TestCase
{
    private JobSearcher $searcher;
    private SearchJobsQueryHandler $handler;

    protected function setUp(): void
    {
        $this->searcher = $this->createMock(JobSearcher::class);
        $this->handler  = new SearchJobsQueryHandler($this->searcher);
    }

    public function test_returns_jobs_response(): void
    {
        $this->searcher->expects($this->once())->method('searchAll')->willReturn(new Jobs([]));

        $result = ($this->handler)(new SearchJobsQuery());

        $this->assertInstanceOf(JobsResponse::class, $result);
    }

    public function test_response_is_wrapped_under_jobs_key(): void
    {
        $this->searcher->expects($this->once())->method('searchAll')->willReturn(new Jobs([]));

        $result = ($this->handler)(new SearchJobsQuery());

        $this->assertArrayHasKey('jobs', $result->toArray());
    }

    public function test_returns_empty_jobs_array_when_no_jobs(): void
    {
        $this->searcher->expects($this->once())->method('searchAll')->willReturn(new Jobs([]));

        $result = ($this->handler)(new SearchJobsQuery());

        $this->assertSame([], $result->toArray()['jobs']);
    }

    public function test_delegates_to_searcher_search_all(): void
    {
        $this->searcher
            ->expects($this->once())
            ->method('searchAll')
            ->willReturn(new Jobs([]));

        ($this->handler)(new SearchJobsQuery());
    }

    public function test_returns_populated_jobs_when_jobs_exist(): void
    {
        $jobs = Jobs::fromArray([
            ['id' => '550e8400-e29b-41d4-a716-000000000041', 'name' => 'Cajero'],
        ]);
        $this->searcher->expects($this->once())->method('searchAll')->willReturn($jobs);

        $result = ($this->handler)(new SearchJobsQuery());

        $this->assertCount(1, $result->toArray()['jobs']);
        $this->assertSame('Cajero', $result->toArray()['jobs'][0]['name']);
    }
}
