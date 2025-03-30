<?php

namespace SuperVMar\User\Domain\Entity;

final readonly class Allocation
{
    public function __construct(
        private Supermarket $supermarket,
        private Job         $job,
    )
    {
    }

    public function supermarket(): Supermarket
    {
        return $this->supermarket;
    }

    public function job(): Job
    {
        return $this->job;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            Supermarket::fromArray([
                'id' => $data['idSupermarket'],
                'name' => $data['nameSupermarket'] ?? null,
            ]),
            Job::fromArray([
                'id' => $data['idJob'],
                'name' => $data['nameJob'] ?? null,
            ]),
        );
    }

    public function toArray(): array
    {
        return [
            'supermarket' => $this->supermarket->toArray(),
            'job' => $this->job->toArray(),
        ];
    }
}