<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdressesStore
 *
 * @ORM\Table(name="adresses_store")
 * @ORM\Entity
 */
class AdressesStore
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="rue", type="string", length=255, nullable=false)
     */
    private $rue;

    /**
     * @var string
     *
     * @ORM\Column(name="residence", type="string", length=255, nullable=false)
     */
    private $residence;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=255, nullable=false)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="gouvernaurat", type="string", length=255, nullable=false)
     */
    private $gouvernaurat;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=255, nullable=false)
     */
    private $pays;

    /**
     * @ORM\ManyToOne(targetEntity=Stores::class, inversedBy="adressesStore")
     */
    private $store;

    public function getStore(): ?Stores
    {
        return $this->store;
    }

    public function setStore(?Stores $store): self
    {
        $this->store = $store;

        return $this;
    }


}
