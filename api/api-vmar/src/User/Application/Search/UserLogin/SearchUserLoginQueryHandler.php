<?php

namespace SuperVMar\User\Application\Search\UserLogin;

use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;
use SuperVMar\User\Domain\Service\UserSearcher;
use SuperVMar\User\Domain\ValueObject\Username;

final readonly class SearchUserLoginQueryHandler implements QueryHandler
{
    public function __construct(
        private UserSearcher $searcher
    ){
    }

    public function __invoke(SearchUserLoginQuery $query): UserLoginResponse
    {
        $username = new Username($query->username());

        $user = $this->searcher->searchByUsername($username);

        return new UserLoginResponse(
            $user->id()->value(),
            $user->username()->value(),
            $user->password()->value(),
            $user->isAdmin()->value(),
        );
    }
}