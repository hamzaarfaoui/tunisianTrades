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
}
