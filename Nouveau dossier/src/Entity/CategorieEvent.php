<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategorieEvent
 *
 * @ORM\Table(name="categorie_event")
 * @ORM\Entity
 */
class CategorieEvent
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_cat_event", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCatEvent;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_categorie_ev", type="string", length=200, nullable=false)
     */
    private $nomCategorieEv;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     */
    private $description;

    public function getIdCatEvent(): ?int
    {
        return $this->idCatEvent;
    }

    public function getNomCategorieEv(): ?string
    {
        return $this->nomCategorieEv;
    }

    public function setNomCategorieEv(string $nomCategorieEv): self
    {
        $this->nomCategorieEv = $nomCategorieEv;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }


}
