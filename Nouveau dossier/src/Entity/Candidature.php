<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Candidature
 *
 * @ORM\Table(name="candidature")
 * @ORM\Entity
 */
class Candidature
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_candidature", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups("post:read")
     */
    private $idCandidature;

    /**
     * @var int
     *
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     * @Groups("post:read")
     */
    private $idUser;

    /**
     * @var int
     *
     * @ORM\Column(name="id_offre", type="integer", nullable=false)
     * @Groups("post:read")
     */
    private $idOffre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cvpath", type="string", length=150, nullable=true)
     * @Groups("post:read")
     */
    private $cvpath;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     * @Groups("post:read")
     */
    private $date;

    /**
     * @var string
     *
     * @Assert\Length(
     *      min = 10,
     *      max = 800,
     *      minMessage = "Your Description must be at least 10 characters long",
     *      maxMessage = "Your Description is too long "
     * )
     * @Assert\NotBlank(message="A description is required")
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     * @Groups("post:read")
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="EtatCandidat", type="string", length=20, nullable=true)
     * @Groups("post:read")
     */
    private $etatcandidat;

    public function getIdCandidature(): ?int
    {
        return $this->idCandidature;
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

    public function getIdOffre(): ?int
    {
        return $this->idOffre;
    }

    public function setIdOffre(int $idOffre): self
    {
        $this->idOffre = $idOffre;

        return $this;
    }

    public function getCvpath(): ?string
    {
        return $this->cvpath;
    }

    public function setCvpath(?string $cvpath): self
    {
        $this->cvpath = $cvpath;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getEtatcandidat(): ?string
    {
        return $this->etatcandidat;
    }

    public function setEtatcandidat(?string $etatcandidat): self
    {
        $this->etatcandidat = $etatcandidat;

        return $this;
    }


}
