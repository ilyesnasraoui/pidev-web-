<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Avis
 *
 * @ORM\Table(name="avis", indexes={@ORM\Index(name="id_produit", columns={"id_produit"})})
 * @ORM\Entity(repositoryClass="App\Repository\AvisRepository")
 */
class Avis
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_avis", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idAvis;

    /**
     * @var int
     *
     * @ORM\Column(name="id_produit", type="integer", nullable=false)
     * @Groups("post:read")
     */
    private $idProduit;

    /**
     * @var string
     *
     * @ORM\Column(name="type_avis", type="string", length=11, nullable=false)
     * @Groups("post:read")
     */
    private $typeAvis;

    public function getIdAvis(): ?int
    {
        return $this->idAvis;
    }
    public function setIdAvis(int $idAvis): self
    {
        $this->idAvis = $idAvis;

        return $this;
    }

    public function getIdProduit(): ?int
    {
        return $this->idProduit;
    }

    public function setIdProduit(int $idProduit): self
    {
        $this->idProduit = $idProduit;

        return $this;
    }

    public function getTypeAvis(): ?string
    {
        return $this->typeAvis;
    }

    public function setTypeAvis(string $typeAvis): self
    {
        $this->typeAvis = $typeAvis;

        return $this;
    }


}
