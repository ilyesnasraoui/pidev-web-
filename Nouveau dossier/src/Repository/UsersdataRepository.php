<?php

namespace App\Repository;

use App\Entity\Usersdata;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Usersdata|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usersdata|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usersdata[]    findAll()
 * @method Usersdata[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersdataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usersdata::class);
    }

    // /**
    //  * @return Usersdata[] Returns an array of Usersdata objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Usersdata
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


   public function findOneByUserId($value): ?Usersdata
   {
       return $this->createQueryBuilder('u')
           ->andWhere('u.idUser = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }


}
