<?php

namespace SuperVMar\AllocateWorker\Domain;

use SuperVMar\Shared\Domain\AggregateRoot;
use SuperVMar\Shared\Domain\ValueObject\Id;

final class WorkerAllocation extends AggregateRoot
{
    public function __construct(
        private Id $idUser,
        private Id $idSupermarket,
        private Id $idJob
    )
    {
    }

    public function idUser(): Id
    {
        return $this->idUser;
    }

    public function idSupermarket(): Id
    {
        return $this->idSupermarket;
    }

    public function idJob(): Id
    {
        return $this->idJob;
    }

    public static function create(
        Id $idUser,
        Id $idSupermarket,
        Id $idJob
    ): self{
        return new self(
            $idUser,
            $idSupermarket,
            $idJob
        );
    }

    public function changeJob(Id $idJob): void
    {
        if (!$this->idJob->equals($idJob)) {
            $this->idJob = $idJob;
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['idUser']),
            new Id($data['idSupermarket']),
            new Id($data['idJob'])
        );
    }

}