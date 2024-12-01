<?php

namespace App\Repository;

use App\Entity\LineaDeFactura;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LineaDeFactura|null find($id, $lockMode = null, $lockVersion = null)
 * @method LineaDeFactura|null findOneBy(array $criteria, array $orderBy = null)
 * @method LineaDeFactura[]    findAll()
 * @method LineaDeFactura[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LineaDeFacturaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LineaDeFactura::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(LineaDeFactura $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(LineaDeFactura $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return LineaDeFactura[] Returns an array of LineaDeFactura objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LineaDeFactura
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
