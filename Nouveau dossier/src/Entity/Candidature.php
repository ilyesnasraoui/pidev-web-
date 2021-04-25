<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Candidature
 *
 * @ORM\Table(name="candidature")
 * @ORM\Entity(repositoryClass="App\Repository\CandidatureRepository")
 */
class Candidature
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_candidature", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCandidature;

    /**
     * @var int
     *
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     */
    private $idUser;

    /**
     * @var int
     *
     * @ORM\Column(name="id_offre", type="integer", nullable=false)
     */
    private $idOffre;

    /**
     * @var string|null
     *
     *
     * @ORM\Column(name="cvpath", type="string", length=150, nullable=true)
     */
    private $cvpath;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var string
     * @Assert\NotBlank(message="A description is required")
     * @Assert\Length(
     *      min = 10,
     *      max = 80,
     *      minMessage = "Your Description must be at least 10 characters long",
     *      maxMessage = "Your Description is too long "
     * )
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     */
    private $description;

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


}
