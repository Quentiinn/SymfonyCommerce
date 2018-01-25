<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommercantRepository")
 * @ORM\Table(name="commercants")
 */
class Commercant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $noms;

    /**
     * @var date
     * @ORM\Column(type="date")
     */
    private $dateInstallation;

    /**
     * @var float
     * @ORM\Column(type="float", precision=8 , scale=2)
     */
    private $prixLocation;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="TypeCommercant")
     * @ORM\JoinColumn(name="id_type_commercant", referencedColumnName="id")
     */
    private $idTypeCommercant;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Commercant
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getNoms(): string
    {
        return $this->noms;
    }

    /**
     * @param string $noms
     * @return Commercant
     */
    public function setNoms(string $noms): Commercant
    {
        $this->noms = $noms;
        return $this;
    }

    /**
     * @return date
     */
    public function getDateInstallation(): \DateTime
    {
        return $this->dateInstallation;
    }

    /**
     * @param \DateTime $dateInstallation
     * @return Commercant
     */
    public function setDateInstallation(\DateTime $dateInstallation): Commercant
    {
        $this->dateInstallation = $dateInstallation;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrixLocation(): float
    {
        return $this->prixLocation;
    }

    /**
     * @param float $prixLocation
     * @return Commercant
     */
    public function setPrixLocation(float $prixLocation): Commercant
    {
        $this->prixLocation = $prixLocation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdTypeCommercant()
    {
        return $this->idTypeCommercant;
    }

    /**
     * @param int $idTypeCommercant
     * @return Commercant
     */
    public function setIdTypeCommercant($idTypeCommercant): Commercant
    {
        $this->idTypeCommercant = $idTypeCommercant;
        return $this;
    }

}
