<?php

namespace SuperVMar\Job\Domain;

use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

interface JobRepository
{
    public function insert(Job $job): void;
    public function update(Job $job): void;
    public function delete(Id $idJob): void;
    /**
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): Jobs;
    /**
     * @throws ItemNotFoundException
     */
    public function checkAllocationsExists(Id $idJob): void;
}