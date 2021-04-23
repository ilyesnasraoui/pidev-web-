<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Usersdata
 *
 * @ORM\Table(name="usersdata")
 * @ORM\Entity(repositoryClass="App\Repository\UsersdataRepository")
 */
class Usersdata
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_data", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idData;

    /**
     * @var int
     *
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     */
    private $idUser;

    /**
     * @var int
     *
     * @ORM\Column(name="account_verif", type="integer", nullable=false)
     */
    private $accountVerif;

    /**
     * @var int
     *
     * @ORM\Column(name="forget_pwd", type="integer", nullable=false)
     */
    private $forgetPwd;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=false)
     */
    private $image;

    /**
     * @return int
     */
    public function getIdData(): int
    {
        return $this->idData;
    }

    /**
     * @param int $idData
     */
    public function setIdData(int $idData): void
    {
        $this->idData = $idData;
    }

    /**
     * @return int
     */
    public function getIdUser(): int
    {
        return $this->idUser;
    }

    /**
     * @param int $idUser
     */
    public function setIdUser(int $idUser): void
    {
        $this->idUser = $idUser;
    }

    /**
     * @return int
     */
    public function getAccountVerif(): int
    {
        return $this->accountVerif;
    }

    /**
     * @param int $accountVerif
     */
    public function setAccountVerif(int $accountVerif): void
    {
        $this->accountVerif = $accountVerif;
    }

    /**
     * @return int
     */
    public function getForgetPwd(): int
    {
        return $this->forgetPwd;
    }

    /**
     * @param int $forgetPwd
     */
    public function setForgetPwd(int $forgetPwd): void
    {
        $this->forgetPwd = $forgetPwd;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }


}
