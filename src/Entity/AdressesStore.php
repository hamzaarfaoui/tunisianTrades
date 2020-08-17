<?php

namespace App\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Document
 */
class AdressesStore
{
    /**
     * @ORM\Id
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $rue;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $residence;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $ville;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $gouvernaurat;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $pays;
    
    /** 
     * @ORM\ReferenceOne(targetDocument="Stores" ,inversedBy="adressesStore") */
    protected $store;
    
    /**      * @return mixed      */
    public function getId()
    {
        return $this->id;
    }
    
    /**      * @return mixed      */
    public function getRue()
    {
        return $this->rue;
    }
    /**      * @param mixed $rue      */
    public function setRue($rue)
    {
        $this->rue = $rue;
    }
    
    /**      * @return mixed      */
    public function getVille()
    {
        return $this->ville;
    }
    /**      * @param mixed $ville      */
    public function setVille($ville)
    {
        $this->ville = $ville;
    }
    
    /**      * @return mixed      */
    public function getGouvernaurat()
    {
        return $this->gouvernaurat;
    }
    /**      * @param mixed $gouvernaurat      */
    public function setGouvernaurat($gouvernaurat)
    {
        $this->gouvernaurat = $gouvernaurat;
    }
    
    /**      * @return mixed      */
    public function getPays()
    {
        return $this->pays;
    }
    /**      * @param mixed $pays      */
    public function setPays($pays)
    {
        $this->pays = $pays;
    }
    
    /**      * @return mixed      */
    public function getResidence()
    {
        return $this->residence;
    }
    /**      * @param mixed $residence      */
    public function setResidence($residence)
    {
        $this->residence = $residence;
    }
    
    
    /**
     * @param Stores $store
     *
     * @return self
     */
    public function setStore(Stores $store)
    {
        $this->store = $store;
        return $this;
    }
    
    /**
     * Get store
     *
     */
    public function getStore()
    {
        return $this->store;
    }
}
