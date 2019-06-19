<?php

namespace App\Repository;
use Doctrine\ODM\MongoDB\DocumentRepository;
use App\Utils\EntityDocumentManager;
date_default_timezone_set("Europe/Paris");
/**
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends DocumentRepository
{
    public function findArray($array)
    {
        $qb = $this->createQueryBuilder('Products')
                ->Select('id', 'name', 'price', 'image', 'store', 'pricePromotion')
                ->field('_id')->in($array);

        return $qb->getQuery()->execute();
    }
    
    public function byNbrViews($store)
    {
        $qb = $this->createQueryBuilder('Products')
                ->field('store')->equals($store)
                ->sort('nbrView', 'desc')
                ->field('nbrView')->gt(0)
                ->limit(6);
        return $qb->getQuery()->execute();
    }
    
    public function byNbrAddToCart($store)
    {
        $qb = $this->createQueryBuilder('Products')
                ->field('store')->equals($store)
                ->sort('nbrAddToCart', 'desc')
                ->field('nbrAddToCart')->gt(0)
                ->limit(6);
        return $qb->getQuery()->execute();
    }
    
    public function byNbrAddToFavorite($store)
    {
        $qb = $this->createQueryBuilder('Products')
                ->field('store')->equals($store)
                ->sort('nbrAddToFavorite', 'desc')
                ->field('nbrAddToFavorite')->gt(0)
                ->limit(6);
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
                $qb->sort('price', 'DESC');
            }elseif ($params['tri'] == 2){
                $qb->sort('price', 'ASC');
            }elseif ($params['tri'] == 3){
                $qb->sort('nbrView', 'DESC');
            }
        }
        return $qb->getQuery()->execute();
    }
    
    private function addFilters($qb, $params)
    {
        
        if(!empty($params['tri'])){
            if ($params['tri'] == 1){
                $qb->sort('price', 'DESC');
            }elseif ($params['tri'] == 2){
                $qb->sort('price', 'ASC');
            }elseif ($params['tri'] == 3){
                $qb->sort('nbrView', 'DESC');
            }
        }
        
        
       return $qb;
    }

    
//    public function byPriceAsc()
//    {
//        $qb = $this->createQueryBuilder('Products')
//                ->sort('price', 'asc');
//        return $qb->getQuery()->execute();
//    }
//    
//    public function byPulaires()
//    {
//        $qb = $this->createQueryBuilder('Products')
//                ->sort('nbrView', 'desc');
//        return $qb->getQuery()->execute();
//    }
}
