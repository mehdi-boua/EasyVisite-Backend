<?php

namespace App\Entity;

use App\Repository\PharmacieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PharmacieRepository::class)]
class Pharmacie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $officine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commune = null;

    #[ORM\Column(length: 255)]
    private ?string $departement = null;

    #[ORM\ManyToMany(targetEntity: Grossiste::class)]
    private Collection $listeGrossistes;

    public function __construct()
    {
        $this->listeGrossistes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOfficine(): ?string
    {
        return $this->officine;
    }

    public function setOfficine(string $officine): self
    {
        $this->officine = $officine;

        return $this;
    }

    public function getCommune(): ?string
    {
        return $this->commune;
    }

    public function setCommune(?string $commune): self
    {
        $this->commune = $commune;

        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(string $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    /**
     * @return Collection<int, Grossiste>
     */
    public function getListeGrossistes(): Collection
    {
        return $this->listeGrossistes;
    }

    public function addListeGrossiste(Grossiste $listeGrossiste): self
    {
        if (!$this->listeGrossistes->contains($listeGrossiste)) {
            $this->listeGrossistes->add($listeGrossiste);
        }

        return $this;
    }

    public function removeListeGrossiste(Grossiste $listeGrossiste): self
    {
        $this->listeGrossistes->removeElement($listeGrossiste);

        return $this;
    }
}
