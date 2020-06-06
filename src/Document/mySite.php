<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document(repositoryClass="App\Repository\MySiteRepository")
 */
class MySite
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $nbrVisite;
    
    /**      * @return mixed      */
    public function getId()
    {
        return $this->id;
    }
    
    /**      * @return mixed      */
    public function getNbrVisite()
    {
        return $this->nbrVisite;
    }
    /**      * @param mixed $nbrVisite      */
    public function setNbrVisite($nbrVisite)
    {
        $this->nbrVisite = $nbrVisite;
    }
}
