<?php

namespace SuperVMar\Notification\Infrastructure;

use Doctrine\DBAL\Connection;
use SuperVMar\Notification\Domain\RestockerFinder;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class DbalRestockerFinder implements RestockerFinder
{
    public function __construct(private Connection $connection)
    {
    }

    /** @return Id[] */
    public function findRestockerUserIdsByZone(Id $idZone): array
    {
        $idSupermarket = $this->connection->createQueryBuilder()
            ->select('idSupermarket')
            ->from('zone')
            ->where('id = :idZone')
            ->setParameter('idZone', $idZone->value())
            ->executeQuery()
            ->fetchOne();

        if (!$idSupermarket) {
            return [];
        }

        $userIds = $this->connection->createQueryBuilder()
            ->select('wa.idUser')
            ->from('worker_allocation', 'wa')
            ->join('wa', 'job', 'j', 'j.id = wa.idJob')
            ->where('wa.idSupermarket = :idSupermarket')
            ->andWhere('j.name LIKE :jobName')
            ->setParameter('idSupermarket', $idSupermarket)
            ->setParameter('jobName', '%eponedor%')
            ->executeQuery()
            ->fetchFirstColumn();

        return array_map(fn(string $id) => new Id($id), $userIds);
    }
}
