<?php

namespace App\Entity;


use DateTime;

class FournisseurRecherche
{
    /**
     * @var string|null
     */
    private $nom;

    /**
     * @var DateTime|null
     */
    private $dateMini;

    /**
     * @var DateTime|null
     */
    private $dateMaxi;

    /**
     * @return string|null
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * @param string|null $nom
     */
    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return DateTime|null
     */
    public function getDateMini(): ?DateTime
    {
        return $this->dateMini;
    }

    /**
     * @param DateTime|null $dateMini
     */
    public function setDateMini(?DateTime $dateMini): void
    {
        $this->dateMini = $dateMini;
    }

    /**
     * @return DateTime|null
     */
    public function getDateMaxi(): ?DateTime
    {
        return $this->dateMaxi;
    }

    /**
     * @param DateTime|null $dateMaxi
     */
    public function setDateMaxi(?DateTime $dateMaxi): void
    {
        $this->dateMaxi = $dateMaxi;
    }
}
