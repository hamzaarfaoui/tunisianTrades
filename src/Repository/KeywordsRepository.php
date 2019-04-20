<?php

namespace App\Repository;
use Doctrine\ODM\MongoDB\DocumentRepository;
use App\Utils\EntityDocumentManager;
date_default_timezone_set("Europe/Paris");
/**
 * @method Keywords|null find($id, $lockMode = null, $lockVersion = null)
 * @method Keywords|null findOneBy(array $criteria, array $orderBy = null)
 * @method Keywords[]    findAll()
 * @method Keywords[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KeywordsRepository extends DocumentRepository
{
    public function byName($chaine)
    {
        $qb = $this->createQueryBuilder('Keywords')
                ->Select('id', 'name')
                ->field('name')->equals(new \MongoRegex('/.*'.$chaine.'.*/i'));

        return $qb->getQuery()->execute();
    }
}
