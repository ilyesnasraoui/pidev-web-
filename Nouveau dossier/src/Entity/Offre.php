<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Offre
 *
 * @ORM\Table(name="offre")
 * @ORM\Entity
 */
class Offre
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_offre", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idOffre;

    /**
     * @var int
     *
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     */
    private $idUser;

    /**
     * @var string|null
     *@Assert\NotBlank(message="Offre must have an image")
     * @ORM\Column(name="offreimgpath", type="string", length=150, nullable=true)
     */
    private $offreimgpath;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var string
     *@Assert\Length(
     *      min = 10,
     *      max = 800,
     *      minMessage = "Your Description must be at least 10 characters long",
     *      maxMessage = "Your Description is too long "
     * )
     * @Assert\NotBlank(message="A description is required")
     * @ORM\Column(name="description", type="string", length=1000, nullable=false)
     */
    private $description;

    /**
     * @var string|null
     * @Assert\Length(
     *      min = 2,
     *      max = 30,
     *      minMessage = "Your title must be at least 2 characters long",
     *      maxMessage = "Your title cannot be longer than 30 characters"
     * )
      * @Assert\NotBlank(message="A Title is required")
     * @ORM\Column(name="titre", type="string", length=50, nullable=true)
     */
    private $titre;

    public function getIdOffre(): ?int
    {
        return $this->idOffre;
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

    public function getOffreimgpath(): ?string
    {
        return $this->offreimgpath;
    }

    public function setOffreimgpath(?string $offreimgpath): self
    {
        $this->offreimgpath = $offreimgpath;

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

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }


}
