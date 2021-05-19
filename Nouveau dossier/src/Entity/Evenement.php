<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;




/**
 * Evenement
 *
 * @ORM\Table(name="evenement")
 * @ORM\Entity(repositoryClass="App\Repository\EvenementRepository")
 */
class Evenement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_evenement", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups("post:read")
     */
    private $idEvenement;

    /**
     * @var int
     *
     * @ORM\Column(name="id_cat_evenement", type="integer", nullable=false)
     * @Groups("post:read")
     */
    private $idCatEvenement;

    /**
     * @var int
     *
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     * @Groups("post:read")
     */
    private $idUser;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_evenement", type="string", length=100, nullable=false)
     * @Groups("post:read")
     */
    private $nomEvenement;

    /**
     * @var string
     *
     * @ORM\Column(name="date_evenement", type="string", length=100, nullable=false)
     * @Groups("post:read")
     */
    private $dateEvenement;

    /**
     * @var int
     * @ORM\Column(name="duree_evenement", type="integer", nullable=false)
     * @Groups("post:read")
     */
    private $dureeEvenement;

    /**
     * @var string
     *
     * @ORM\Column(name="image_evnement", type="string", length=200, nullable=false)
     * @Groups("post:read")
     */
    private $imageEvnement;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     * @Groups("post:read")
     */
    private $description;

    /**
     * @var int|null
     *
     * @ORM\Column(name="validate", type="integer", nullable=true)
     * @Groups("post:read")
     */
    private $validate;

    public function getIdEvenement(): ?int
    {
        return $this->idEvenement;
    }

    public function getIdCatEvenement(): ?int
    {
        return $this->idCatEvenement;
    }

    public function setIdCatEvenement(int $idCatEvenement): self
    {
        $this->idCatEvenement = $idCatEvenement;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getNomEvenement(): ?string
    {
        return $this->nomEvenement;
    }

    public function setNomEvenement(string $nomEvenement): self
    {
        $this->nomEvenement = $nomEvenement;

        return $this;
    }

    public function getDateEvenement(): ?string
    {
        return $this->dateEvenement;
    }

    public function setDateEvenement(string $dateEvenement): self
    {
        $this->dateEvenement = $dateEvenement;

        return $this;
    }

    public function getDureeEvenement(): ?int
    {
        return $this->dureeEvenement;
    }

    public function setDureeEvenement(int $dureeEvenement): self
    {
        $this->dureeEvenement = $dureeEvenement;

        return $this;
    }

    public function getImageEvnement(): ?string
    {
        return $this->imageEvnement;
    }

    public function setImageEvnement(string $imageEvnement): self
    {
        $this->imageEvnement = $imageEvnement;

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

    public function getValidate(): ?int
    {
        return $this->validate;
    }

    public function setValidate(?int $validate): self
    {
        $this->validate = $validate;

        return $this;
    }


}
