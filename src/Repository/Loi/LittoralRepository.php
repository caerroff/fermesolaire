<?php

namespace App\Repository\Loi;

use App\Entity\Loi\Littoral;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Littoral>
 *
 * @method Littoral|null find($id, $lockMode = null, $lockVersion = null)
 * @method Littoral|null findOneBy(array $criteria, array $orderBy = null)
 * @method Littoral[]    findAll()
 * @method Littoral[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LittoralRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Littoral::class);
    }

    public function truncate()
    {
        $this->createQueryBuilder('l')
            ->delete()
            ->getQuery()
            ->execute();
    }

    //    /**
    //     * @return Littoral[] Returns an array of Littoral objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Littoral
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
