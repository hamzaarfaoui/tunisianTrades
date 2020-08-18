<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    public function findArray($array)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->add('where', $qb->expr()->in('u.id', ':my_array'))
        ->setParameter('my_array', $array);;

        return $qb->getQuery()->execute();
    }
    
    public function produitsLiees($params)
    {
        $qb = $this->createQueryBuilder('u')
                ->where('u.id != :id')
                ->where('u.sousCategorie = :sousCategorie')
                ->setMaxResults(4)
                ->setParameter('id', $params['id'])
                ->setParameter('id', $params['sousCategorie']);

        return $qb->getQuery()->execute();
    }
    
    public function byQB($params, $disponible)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->where('u.name LIKE :name')
                ->setParameter('name', '%'.$params.'%');
        $this->addFilters($qb, $disponible);
        return $qb->getQuery()->execute();
    }
    
    private function addFilters($qb, $params)
    {
        if($params == 'oui'){
            $qb->andWhere('u.qte > 0');
        }
        
        
       return $qb;
    }
    
    public function newProducts()
    {
        $qb = $this->createQueryBuilder('u')
                ->orderBy('u.createdAt', 'desc');
        return $qb->getQuery()->execute();
    }
    
    public function venteFlash()
    {
        $qb = $this->createQueryBuilder('u')
                ->where('u.pricePromotion > 0')
                ->orderBy('u.createdAt', 'desc');
        return $qb->getQuery()->execute();
    }
    
    public function inPromotion()
    {
        $qb = $this->createQueryBuilder('u')
                ->where('u.pricePromotion > 0')
                ->orderBy('u.createdAt', 'desc');
        return $qb->getQuery()->execute();
    }
    
    public function byNbrViews($store)
    {
        $qb = $this->createQueryBuilder('u')
                ->where('u.store = :store')
                ->orderBy('u.nbrView', 'desc')
                ->andWhere('u.nbrView > 0')
                ->setMaxResults(6)
                ->setParameter('store', $store);
        return $qb->getQuery()->execute();
    }
    /**
    on est arrivé içi
    */
    public function byNbrAddToCart($store)
    {
        $qb = $this->createQueryBuilder('Products')
                ->field('store')->equals($store)
                ->orderBy('nbrAddToCart', 'desc')
                ->field('nbrAddToCart')->gt(0)
                ->limit(6);
        return $qb->getQuery()->execute();
    }
    
    public function byNbrAddToFavorite($store)
    {
        $qb = $this->createQueryBuilder('Products')
                ->field('store')->equals($store)
                ->orderBy('nbrAddToFavorite', 'desc')
                ->field('nbrAddToFavorite')->gt(0)
                ->limit(6);
        return $qb->getQuery()->execute();
    }
    
    public function byPosAndSc($sousCategorie)
    {
        $qb = $this->createQueryBuilder('Products')
                ->field('sousCategorie.id')->equals($sousCategorie)
                ->orderBy('position', 'ASC');
        return $qb->getQuery()->execute();
    }
    
    public function liees($sousCategorie)
    {
        $qb = $this->createQueryBuilder('Products')
                ->field('sousCategorie.id')->equals($sousCategorie)
                ->orderBy('price', 'ASC');
        return $qb->getQuery()->execute();
    }
    
    public function byStore($store)
    {
        $qb = $this->createQueryBuilder('Products')
                ->field('store.id')->equals($store)
                ->orderBy('createdAt', 'desc');
        return $qb->getQuery()->execute();
    }
    
    public function byCategorie($params)
    {
        $qb = $this->createQueryBuilder('Products');
                
        if ((isset($params['minimum']) && !empty($params['minimum'])) && (isset($params['minimum'])&&!empty($params['minimum']))){
            $qb->field('price')->range($params['minimum'],$params['maximum']);
        }
        if(isset($params['tri'])&&!empty($params['tri'])){
            if ($params['tri'] == 1){
                $qb->orderBy('price', 'DESC');
            }elseif ($params['tri'] == 2){
                $qb->orderBy('price', 'ASC');
            }elseif ($params['tri'] == 3){
                $qb->orderBy('nbrView', 'DESC');
            }
        }
        if(isset($params['categorie'])&&!empty($params['categorie'])){
            $qb->field('sousCategorie.id')->equals($params['categorie']);
        }
        if(isset($params['store'])&&!empty($params['store'])){
            $qb->field('store.id')->equals($params['store']);
        }
        if(isset($params['categories'])&&!empty($params['categories'])){
            $qb->field('sousCategorie.id')->in($params['categories']);
        }
        return $qb->getQuery()->execute();
    }
    
    
    
    public function byKeyword($keyword)
    {
        $qb = $this->createQueryBuilder('Products')
                ->field('keywords.id')->in($keyword)
                ->orderBy('createdAt', 'desc');
        return $qb->getQuery()->execute();
    }

    
//    public function byPriceAsc()
//    {
//        $qb = $this->createQueryBuilder('Products')
//                ->orderBy('price', 'asc');
//        return $qb->getQuery()->execute();
//    }
//    
//    public function byPulaires()
//    {
//        $qb = $this->createQueryBuilder('Products')
//                ->orderBy('nbrView', 'desc');
//        return $qb->getQuery()->execute();
//    }
    
    // /**
    //  * @return Products[] Returns an array of Products objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Products
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
