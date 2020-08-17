<?php

namespace App\Repository;

use App\Entity\Commandes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Commandes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commandes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commandes[]    findAll()
 * @method Commandes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commandes::class);
    }
    public function liste()
    {
        $qb = $this->createQueryBuilder('Commandes')
                ->sort('createdAt', 'desc');
        return $qb->getQuery()->execute();
    }
    public function nombreCmdValide()
    {
        $qb = $this->createQueryBuilder('Commandes')
                ->field('status')->equals(1);
        return $qb->getQuery()->execute()->count();
    }
    public function nombreCmdEnCours()
    {
        $qb = $this->createQueryBuilder('Commandes')
                ->field('status')->equals(0);
        return $qb->getQuery()->execute()->count();
    }
    
    public function listeInDash()
    {
        $qb = $this->createQueryBuilder('Commandes')
                ->sort('createdAt', 'desc')
                ->limit(6);
        return $qb->getQuery()->execute();
    }
    // /**
    //  * @return Commandes[] Returns an array of Commandes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Commandes
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
