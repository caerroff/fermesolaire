<?php

namespace App\Repository;

use App\Entity\Relais;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Relais>
 *
 * @method Relais|null find($id, $lockMode = null, $lockVersion = null)
 * @method Relais|null findOneBy(array $criteria, array $orderBy = null)
 * @method Relais[]    findAll()
 * @method Relais[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelaisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Relais::class);
    }

    //    /**
    //     * @return Relais[] Returns an array of Relais objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Relais
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
