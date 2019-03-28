<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class Keywords
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $name;
    
    /** @MongoDB\ReferenceOne(targetDocument="Products", inversedBy="keywords") */
    protected $product;
    
    /** 
     * @MongoDB\ReferenceOne(targetDocument="SousCategories", inversedBy="keywords") */
    protected $categorie = array();
    
    /**      * @return mixed      */
    public function getId()
    {
        return $this->id;
    }
    
    /**      * @return mixed      */
    public function getName()
    {
        return $this->name;
    }
    /**      * @param mixed $name      */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    
    
    
    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }
    /**
     * @param Products $product
     *
     * @return self
     */
    public function setProduct(Products $product)
    {
        $this->product = $product;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getCategorie()
    {
        return $this->categorie;
    }
    /**
     * @param Categories $categorie
     *
     * @return self
     */
    public function setCategorie(SousCategories $categorie)
    {
        $this->categorie = $categorie;
        return $this;
    }
}
