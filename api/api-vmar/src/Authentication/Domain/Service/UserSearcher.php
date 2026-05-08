<?php

namespace SuperVMar\Authentication\Domain\Service;

use SuperVMar\Authentication\Domain\AuthRepository;
use SuperVMar\Authentication\Domain\AuthUser;
use SuperVMar\Authentication\Domain\ValueObject\Username;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Criteria\Field;
use SuperVMar\Shared\Domain\Criteria\FieldName;
use SuperVMar\Shared\Domain\Criteria\Fields;
use SuperVMar\Shared\Domain\Criteria\Filter;
use SuperVMar\Shared\Domain\Criteria\FilterField;
use SuperVMar\Shared\Domain\Criteria\FilterOperator;
use SuperVMar\Shared\Domain\Criteria\Filters;
use SuperVMar\Shared\Domain\Criteria\FilterValue;
use SuperVMar\Shared\Domain\Criteria\Join;
use SuperVMar\Shared\Domain\Criteria\JoinFirstTable;
use SuperVMar\Shared\Domain\Criteria\Joins;
use SuperVMar\Shared\Domain\Criteria\JoinSecondTable;
use SuperVMar\Shared\Domain\Criteria\JoinType;
use SuperVMar\Shared\Domain\Criteria\On;
use SuperVMar\Shared\Domain\Criteria\OnFirstField;
use SuperVMar\Shared\Domain\Criteria\OnOperator;
use SuperVMar\Shared\Domain\Criteria\OnSecondField;
use SuperVMar\Shared\Domain\Criteria\Select;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;

final readonly class UserSearcher
{
    public function __construct(
        private AuthRepository $repository
    )
    {
    }

    public function search(Username $username): AuthUser
    {
        return $this->repository->search(
            new Criteria(
                filters: new Filters(
                    [
                        new Filter(
                            new FilterField(TableNames::TABLE_USER, new FieldName('username')),
                            FilterOperator::EQUAL,
                            new FilterValue($username)
                        )
                    ]
                ),
                select: new Select(
                    new Fields([
                        new Field(TableNames::TABLE_USER, new FieldName('username')),
                        new Field(TableNames::TABLE_USER, new FieldName('password')),
                    ])
                ),
            )
        );
    }
}