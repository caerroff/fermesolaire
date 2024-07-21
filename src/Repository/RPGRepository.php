<?php

namespace App\Repository;

use App\Entity\RPG;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RPG>
 *
 * @method RPG|null find($id, $lockMode = null, $lockVersion = null)
 * @method RPG|null findOneBy(array $criteria, array $orderBy = null)
 * @method RPG[]    findAll()
 * @method RPG[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RPGRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RPG::class);
    }

    //    /**
    //     * @return RPG[] Returns an array of RPG objects
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

    //    public function findOneBySomeField($value): ?RPG
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
