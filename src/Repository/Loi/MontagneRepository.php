<?php

namespace App\Repository\Loi;

use App\Entity\Loi\Montagne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Montagne>
 *
 * @method Montagne|null find($id, $lockMode = null, $lockVersion = null)
 * @method Montagne|null findOneBy(array $criteria, array $orderBy = null)
 * @method Montagne[]    findAll()
 * @method Montagne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MontagneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Montagne::class);
    }

    public function truncate()
    {
        $this->createQueryBuilder('m')
            ->delete()
            ->getQuery()
            ->execute();
    }

    //    /**
    //     * @return Montagne[] Returns an array of Montagne objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Montagne
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
