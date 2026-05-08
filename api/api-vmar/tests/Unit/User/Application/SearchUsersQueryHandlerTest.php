<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\User\Application;

use SuperVMar\App\Tests\Unit\ApplicationTestCase;
use SuperVMar\User\Application\Search\Users\SearchUsersQuery;
use SuperVMar\User\Application\Search\Users\SearchUsersQueryHandler;
use SuperVMar\User\Application\Search\Users\UsersResponse;
use SuperVMar\User\Domain\Service\UserSearcher;
use SuperVMar\User\Domain\Users;

final class SearchUsersQueryHandlerTest extends ApplicationTestCase
{
    private UserSearcher $searcher;
    private SearchUsersQueryHandler $handler;

    protected function setUp(): void
    {
        $this->searcher = $this->createMock(UserSearcher::class);
        $this->handler  = new SearchUsersQueryHandler($this->searcher);
    }

    public function test_returns_users_response(): void
    {
        $this->searcher->expects($this->once())->method('searchAll')->willReturn(new Users([]));

        $result = ($this->handler)(new SearchUsersQuery());

        $this->assertInstanceOf(UsersResponse::class, $result);
    }

    public function test_delegates_to_searcher_search_all(): void
    {
        $this->searcher
            ->expects($this->once())
            ->method('searchAll')
            ->willReturn(new Users([]));

        ($this->handler)(new SearchUsersQuery());
    }

    public function test_returns_empty_users_array_when_no_users(): void
    {
        $this->searcher->expects($this->once())->method('searchAll')->willReturn(new Users([]));

        $result = ($this->handler)(new SearchUsersQuery());

        $this->assertSame(['users' => []], $result->toArray());
    }
}
