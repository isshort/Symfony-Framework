<?php

namespace App\Repository;

use App\Entity\Drink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Drink|null find($id, $lockMode = null, $lockVersion = null)
 * @method Drink|null findOneBy(array $criteria, array $orderBy = null)
 * @method Drink[]    findAll()
 * @method Drink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Drink::class);
    }
    /**
     * @method Drink[]
     */
    public function findAllGreater($price): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT * FROM student p
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
       // $stmt->execute(['name' => $price]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }
    // /**
    //  * @return Drink[] Returns an array of Drink objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Drink
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
