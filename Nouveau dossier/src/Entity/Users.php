<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;



/**
 * Users
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 */
class Users implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups("post:read")
     */
    private $idUser;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=50, nullable=false)
     * @Groups("post:read")
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=50, nullable=false)
     * @Groups("post:read")
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50, nullable=false)
     * @Groups("post:read")
     */
    private $email;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_salle", type="integer", nullable=true)
     * @Groups("post:read")
     */
    private $idSalle;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fname", type="string", length=50, nullable=true)
     * @Groups("post:read")
     */
    private $fname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lname", type="string", length=50, nullable=true)
     * @Groups("post:read")
     */
    private $lname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="idcard", type="string", length=10, nullable=true)
     * @Groups("post:read")
     */
    private $idcard;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=15, nullable=true)
     * @Groups("post:read")
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=10, nullable=false)
     * @Groups("post:read")
     */
    private $role;

    /**
     * @var int
     *
     * @ORM\Column(name="blocked", type="integer", nullable=false)
     * @Groups("post:read")
     */
    private $blocked = '0';

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }



    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }



    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getIdSalle(): ?int
    {
        return $this->idSalle;
    }

    public function setIdSalle(?int $idSalle): self
    {
        $this->idSalle = $idSalle;

        return $this;
    }

    public function getFname(): ?string
    {
        return $this->fname;
    }

    public function setFname(?string $fname): self
    {
        $this->fname = $fname;

        return $this;
    }

    public function getLname(): ?string
    {
        return $this->lname;
    }

    public function setLname(?string $lname): self
    {
        $this->lname = $lname;

        return $this;
    }

    public function getIdcard(): ?string
    {
        return $this->idcard;
    }

    public function setIdcard(?string $idcard): self
    {
        $this->idcard = $idcard;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getBlocked(): ?int
    {
        return $this->blocked;
    }

    public function setBlocked(int $blocked): self
    {
        $this->blocked = $blocked;

        return $this;
    }
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getSalt(): ?string
    {
        return null;
    }



    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }


}
