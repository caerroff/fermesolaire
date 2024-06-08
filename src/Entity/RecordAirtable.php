<?php

namespace App\Entity;

use App\Repository\RecordAirtableRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecordAirtableRepository::class)]
class RecordAirtable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TYPUrba = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $RPG = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TYPDisRacc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TYPCapRacc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TYPNomRacc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TYPVilleRacc = null;

    #[ORM\ManyToOne(inversedBy: 'recordAirtables')]
    private ?Relais $Relais = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTYPUrba(): ?string
    {
        return $this->TYPUrba;
    }

    public function setTYPUrba(?string $TYPUrba): static
    {
        $this->TYPUrba = $TYPUrba;

        return $this;
    }

    public function getRPG(): ?string
    {
        return $this->RPG;
    }

    public function setRPG(?string $RPG): static
    {
        $this->RPG = $RPG;

        return $this;
    }

    public function getTYPDisRacc(): ?string
    {
        return $this->TYPDisRacc;
    }

    public function setTYPDisRacc(?string $TYPDisRacc): static
    {
        $this->TYPDisRacc = $TYPDisRacc;

        return $this;
    }

    public function getTYPCapRacc(): ?string
    {
        return $this->TYPCapRacc;
    }

    public function setTYPCapRacc(?string $TYPCapRacc): static
    {
        $this->TYPCapRacc = $TYPCapRacc;

        return $this;
    }

    public function getTYPNomRacc(): ?string
    {
        return $this->TYPNomRacc;
    }

    public function setTYPNomRacc(?string $TYPNomRacc): static
    {
        $this->TYPNomRacc = $TYPNomRacc;

        return $this;
    }

    public function getTYPVilleRacc(): ?string
    {
        return $this->TYPVilleRacc;
    }

    public function setTYPVilleRacc(?string $TYPVilleRacc): static
    {
        $this->TYPVilleRacc = $TYPVilleRacc;

        return $this;
    }

    public function getRelais(): ?Relais
    {
        return $this->Relais;
    }

    public function setRelais(?Relais $Relais): static
    {
        $this->Relais = $Relais;

        return $this;
    }
}
