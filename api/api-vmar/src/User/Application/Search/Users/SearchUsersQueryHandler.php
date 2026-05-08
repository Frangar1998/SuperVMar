<?php

namespace SuperVMar\User\Application\Search\Users;

use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;
use SuperVMar\User\Domain\Service\UserSearcher;

final readonly class SearchUsersQueryHandler implements QueryHandler
{
    public function __construct(
        private UserSearcher $searcher
    )
    {
    }

    public function __invoke(SearchUsersQuery $query): UsersResponse
    {

        $users = $this->searcher->searchAll();

        return new UsersResponse(
            $users->toArray(),
        );
    }
}