<?php

namespace SuperVMar\Notification\Domain;

use SuperVMar\Notification\Domain\ValueObject\AuthKey;
use SuperVMar\Notification\Domain\ValueObject\Endpoint;
use SuperVMar\Notification\Domain\ValueObject\P256dhKey;
use SuperVMar\Shared\Domain\AggregateRoot;
use SuperVMar\Shared\Domain\ValueObject\Id;

final class PushSubscription extends AggregateRoot
{
    public function __construct(
        private readonly Id $id,
        private readonly Id $idUser,
        private Endpoint $endpoint,
        private AuthKey $authKey,
        private P256dhKey $p256dhKey,
    )
    {
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function idUser(): Id
    {
        return $this->idUser;
    }

    public function endpoint(): Endpoint
    {
        return $this->endpoint;
    }

    public function authKey(): AuthKey
    {
        return $this->authKey;
    }

    public function p256dhKey(): P256dhKey
    {
        return $this->p256dhKey;
    }

    public function changeEndpoint(Endpoint $endpoint): void
    {
        if (!$this->endpoint->equals($endpoint)) {
            $this->endpoint = $endpoint;
        }
    }

    public function changeAuthKey(AuthKey $authKey): void
    {
        if (!$this->authKey->equals($authKey)) {
            $this->authKey = $authKey;
        }
    }

    public function changeP256dhKey(P256dhKey $p256dhKey): void
    {
        if (!$this->p256dhKey->equals($p256dhKey)) {
            $this->p256dhKey = $p256dhKey;
        }
    }

    public static function create(
        Id        $id,
        Id        $idUser,
        Endpoint  $endpoint,
        AuthKey   $authKey,
        P256dhKey $p256dhKey,
    ): self
    {
        return new self(
            $id,
            $idUser,
            $endpoint,
            $authKey,
            $p256dhKey,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Id($data['idUser']),
            new Endpoint($data['endpoint']),
            new AuthKey($data['authKey']),
            new P256dhKey($data['p256dhKey']),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'idUser' => $this->idUser->value(),
            'endpoint' => $this->endpoint->value(),
            'authKey' => $this->authKey->value(),
            'p256dhKey' => $this->p256dhKey->value(),
        ];
    }
}
