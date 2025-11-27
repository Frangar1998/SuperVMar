<?php

namespace SuperVMar\User\Domain\Service;

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
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\User\Domain\User;
use SuperVMar\User\Domain\UserRepository;
use SuperVMar\User\Domain\Users;
use SuperVMar\User\Domain\ValueObject\Username;

final readonly class UserSearcher
{
    public function __construct(
        private UserRepository $repository
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function search(Id $idUser): User
    {
        return $this->repository->searchByCriteria(
            new Criteria(
                filters: new Filters(
                    [
                        new Filter(
                            new FilterField(TableNames::TABLE_USER, new FieldName('id')),
                            FilterOperator::EQUAL,
                            new FilterValue($idUser)
                        )
                    ]
                ),
                select: $this->getSelect(),
                joins: $this->getJoins()
            )
        )->first();
    }

    /**
     * @throws ItemNotFoundException
     */
    public function searchWithPassword(Id $idUser): User
    {
        return $this->repository->searchByCriteria(
            new Criteria(
                filters: new Filters(
                    [
                        new Filter(
                            new FilterField(TableNames::TABLE_USER, new FieldName('id')),
                            FilterOperator::EQUAL,
                            new FilterValue($idUser)
                        )
                    ]
                ),
                select: $this->getSelect(
                    new Fields([
                        new Field(TableNames::TABLE_USER, new FieldName('password')),
                    ])
                ),
                joins: $this->getJoins()
            )
        )->first();
    }

    /**
     * @throws ItemNotFoundException
     */
    public function searchByUsername(Username $username): User
    {
        return $this->repository->searchByCriteria(
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
                select: $this->getSelect(
                    new Fields([
                        new Field(TableNames::TABLE_USER, new FieldName('password')),
                    ])
                ),
                joins: $this->getJoins()
            )
        )->first();
    }

    /**
     * @throws ItemNotFoundException
     */
    public function searchAll(): Users
    {
        return $this->repository->searchByCriteria(
            new Criteria(
                select: $this->getSelect(),
                joins: $this->getJoins()
            )
        );
    }

    protected function getSelect(?Fields $additionalFields = null): Select
    {
        $fields = new Fields([
            new Field(TableNames::TABLE_USER, new FieldName('id')),
            new Field(TableNames::TABLE_USER, new FieldName('username')),
            new Field(TableNames::TABLE_USER, new FieldName('idUserData')),
            new Field(TableNames::TABLE_USER, new FieldName('isAdmin')),
            new Field(TableNames::TABLE_USER_DATA, new FieldName('name')),
            new Field(TableNames::TABLE_USER_DATA, new FieldName('surname')),
            new Field(TableNames::TABLE_USER_DATA, new FieldName('email')),
            new Field(TableNames::TABLE_USER_DATA, new FieldName('phone')),
            new Field(TableNames::TABLE_USER_DATA, new FieldName('idAddress')),
            new Field(TableNames::TABLE_ADDRESS, new FieldName('name AS nameAddress')),
            new Field(TableNames::TABLE_ADDRESS, new FieldName('postalCode')),
            new Field(TableNames::TABLE_ADDRESS, new FieldName('city')),
            new Field(TableNames::TABLE_ADDRESS, new FieldName('number')),
            new Field(TableNames::TABLE_ADDRESS, new FieldName('province')),
            new Field(TableNames::TABLE_ADDRESS, new FieldName('floor')),
            new Field(TableNames::TABLE_ADDRESS, new FieldName('door')),
            new Field(TableNames::TABLE_ADDRESS, new FieldName('other')),
        ]);

        if (isset($additionalFields)) {
            foreach ($additionalFields as $additionalField) {
                $fields->add($additionalField);
            }
        }

        return new Select($fields);
    }

    protected function getJoins(): Joins
    {
        return new Joins(
            [
                new Join(
                    JoinType::INNER,
                    new JoinFirstTable(TableNames::TABLE_USER->value),
                    new JoinSecondTable(TableNames::TABLE_USER_DATA->value),
                    new On(
                        new OnFirstField(TableNames::TABLE_USER, new FieldName('idUserData')),
                        OnOperator::EQUAL,
                        new OnSecondField(TableNames::TABLE_USER_DATA, new FieldName('id'))
                    )
                ),
                new Join(
                    JoinType::INNER,
                    new JoinFirstTable(TableNames::TABLE_USER_DATA->value),
                    new JoinSecondTable(TableNames::TABLE_ADDRESS->value),
                    new On(
                        new OnFirstField(TableNames::TABLE_USER_DATA, new FieldName('idAddress')),
                        OnOperator::EQUAL,
                        new OnSecondField(TableNames::TABLE_ADDRESS, new FieldName('id'))
                    )
                )
            ]
        );
    }
}