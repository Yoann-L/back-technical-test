<?php

namespace App\Repository;

use App\Entity\OrderAnalyticReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderAnalyticReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderAnalyticReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderAnalyticReport[]    findAll()
 * @method OrderAnalyticReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderAnalyticReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderAnalyticReport::class);
    }

    // /**
    //  * @return OrderAnalyticReport[] Returns an array of OrderAnalyticReport objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderAnalyticReport
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
