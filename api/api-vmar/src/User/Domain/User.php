<?php

namespace SuperVMar\User\Domain;

use SuperVMar\Shared\Domain\AggregateRoot;
use SuperVMar\Shared\Domain\Exception\CannotDeleteException;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\User\Domain\Entity\Allocations;
use SuperVMar\User\Domain\Entity\UserData;
use SuperVMar\User\Domain\Event\UserDeletedDomainEvent;
use SuperVMar\User\Domain\Event\UserSavedDomainEvent;
use SuperVMar\User\Domain\Exception\InvalidPasswordException;
use SuperVMar\User\Domain\ValueObject\IsAdmin;
use SuperVMar\User\Domain\ValueObject\Password;
use SuperVMar\User\Domain\ValueObject\Username;

final class User extends AggregateRoot
{
    public function __construct(
        private readonly Id           $id,
        private Username              $username,
        private UserData              $userData,
        private IsAdmin               $isAdmin,
        private ?Password             $password = null,
        private readonly ?Allocations $allocations = null,
    )
    {
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function username(): Username
    {
        return $this->username;
    }

    public function password(): ?Password
    {
        return $this->password;
    }

    public function isAdmin(): ?IsAdmin
    {
        return $this->isAdmin;
    }

    public function userData(): UserData
    {
        return $this->userData;
    }

    public function allocations(): Allocations
    {
        return $this->allocations;
    }
    
    public static function create(
        Id          $id,
        Username    $username,
        UserData    $userData,
        IsAdmin     $isAdmin,
        Password    $password,
        Allocations $allocations,
    ): self
    {
        $user = new self(
            $id,
            $username,
            $userData,
            $isAdmin,
            $password,
            $allocations
        );

        $user->record(
            new UserSavedDomainEvent(
                $id->value(),
                $allocations->toArray()
            )
        );

        return $user;
    }

    public function update(
        Username    $username,
        UserData    $userData,
        IsAdmin     $isAdmin,
        Allocations $allocations
    ): void
    {
        $this->changeUsername($username);
        $this->changeUserData($userData);
        $this->changeIsAdmin($isAdmin);

        $this->record(
            new UserSavedDomainEvent(
                $this->id->value(),
                $allocations->toArray()
            )
        );
    }

    public function changeUsername(Username $username): void
    {
        if (!$this->username->equals($username)) {
            $this->username = $username;
        }
    }

    public function changePassword(Password $newPassword, Password $currentPassword): void
    {
        if (!$this->password->equals($currentPassword)) {
            throw new InvalidPasswordException(['ERROR_CURRENT_PASSWORD']);
        }
        $this->password = $newPassword;
    }

    public function changeIsAdmin(IsAdmin $isAdmin): void
    {
        if (!$this->isAdmin->equals($isAdmin)) {
            $this->isAdmin = $isAdmin;
        }
    }

    public function changeUserData(UserData $userData): void
    {
        if (!$this->userData->compare($userData)) {
            $this->userData = $userData;
        }
    }

    /**
     * @throws CannotDeleteException
     */
    public function checkIfIsAdminToDelete(): void
    {
        if ($this->isAdmin->value() === 1) {
            throw new CannotDeleteException("User admin cannot be deleted.");
        }

        $this->record(
            new UserDeletedDomainEvent(
                $this->id->value(),
            )
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Username($data['username']),
            UserData::fromArray([
                'id' => $data['idUserData'],
                'name' => $data['name'],
                'surname' => $data['surname'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'idAddress' => $data['idAddress'],
                'nameAddress' => $data['nameAddress'],
                'postalCode' => $data['postalCode'],
                'city' => $data['city'],
                'number' => $data['number'],
                'province' => $data['province'],
                'floor' => $data['floor'],
                'door' => $data['door'],
                'other' => $data['other'],
            ]),
            new IsAdmin($data['isAdmin']),
            isset($data['password']) ? new Password($data['password'], $data['password']) : null,
            array_key_exists('allocations', $data) ? Allocations::fromArray($data['allocations']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'username' => $this->username->value(),
            'userData' => $this->userData->toArray(),
            'isAdmin' => $this->isAdmin->value(),
            'allocations' => $this->allocations?->toArray() ?? [],
        ];
    }
    
}