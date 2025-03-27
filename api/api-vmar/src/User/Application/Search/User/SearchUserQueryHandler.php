<?php

namespace SuperVMar\User\Application\Search\User;

use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;
use SuperVMar\User\Application\Search\UserSearcher;
use SuperVMar\User\Domain\ValueObject\Id;

final readonly class SearchUserQueryHandler implements QueryHandler
{
    public function __construct(
        private UserSearcher $searcher
    ){
    }

    public function __invoke(SearchUserQuery $query): UserResponse
    {
        $id = new Id($query->id());

        $user = $this->searcher->search($id);

        return new UserResponse(
            $user->id()->value(),
            $user->username()->value(),
            $user->userData()->toArray()
        );
    }
}