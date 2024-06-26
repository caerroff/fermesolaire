<?php

namespace App\Entity;

use App\Repository\RecordAirtableRepository;
use Doctrine\DBAL\Types\Types;
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
    private ?string $TYPDisRacc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TYPCapRacc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TYPNomRacc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TYPVilleRacc = null;

    #[ORM\ManyToOne(inversedBy: 'recordAirtables')]
    private ?Relais $Relais = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $RPG = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TYPGhi = null;

    #[ORM\Column(nullable: true)]
    private ?array $TYPEnviro = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ZNIEFF1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ZNIEFF2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $N2000Habitats = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $N2000DOiseaux = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $PNR = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TYPPpri = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TYPZonePpri = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $MH = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ZoneHumide = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $TYPInfoComp = null;

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

    public function setRecord(array $record)
    {
        ksort($record['fields']);
        $this->setTYPUrba($record['fields']['TYP: Urba']);
        $this->setRPG($record['fields']['RPG']);
        $this->setTYPDisRacc($record['fields']['TYP: DistRacc']);
        $this->setTYPCapRacc($record['fields']['TYP: CapaRacc']);
        $this->setTYPNomRacc($record['fields']['TYP: NomRacc']);
        $this->setTYPVilleRacc($record['fields']['TYP: VilleRacc']);
        $this->setTYPUrba($record['fields']['TYP: Urba']);
    }

    public function getRPG(): ?array
    {
        return $this->RPG;
    }

    public function setRPG(?array $RPG): static
    {
        $this->RPG = $RPG;

        return $this;
    }

    public function addRPG(string $value): static
    {
        $this->RPG[] = $value;

        return $this;
    }

    public function getTYPGhi(): ?string
    {
        return $this->TYPGhi;
    }

    public function setTYPGhi(?string $TYPGhi): static
    {
        $this->TYPGhi = $TYPGhi;

        return $this;
    }

    public function getTYPEnviro(): ?array
    {
        return $this->TYPEnviro;
    }

    public function setTYPEnviro(?array $TYPEnviro): static
    {
        $this->TYPEnviro = $TYPEnviro;

        return $this;
    }

    public function getZNIEFF1(): ?string
    {
        return $this->ZNIEFF1;
    }

    public function setZNIEFF1(?string $ZNIEFF1): static
    {
        $this->ZNIEFF1 = $ZNIEFF1;

        return $this;
    }

    public function getZNIEFF2(): ?string
    {
        return $this->ZNIEFF2;
    }

    public function setZNIEFF2(?string $ZNIEFF2): static
    {
        $this->ZNIEFF2 = $ZNIEFF2;

        return $this;
    }

    public function getN2000Habitats(): ?string
    {
        return $this->N2000Habitats;
    }

    public function setN2000Habitats(?string $N2000Habitats): static
    {
        $this->N2000Habitats = $N2000Habitats;

        return $this;
    }

    public function getN2000DOiseaux(): ?string
    {
        return $this->N2000DOiseaux;
    }

    public function setN2000DOiseaux(?string $N2000DOiseaux): static
    {
        $this->N2000DOiseaux = $N2000DOiseaux;

        return $this;
    }

    public function getPNR(): ?string
    {
        return $this->PNR;
    }

    public function setPNR(?string $PNR): static
    {
        $this->PNR = $PNR;

        return $this;
    }

    public function getTYPPpri(): ?string
    {
        return $this->TYPPpri;
    }

    public function setTYPPpri(?string $TYPPpri): static
    {
        $this->TYPPpri = $TYPPpri;

        return $this;
    }

    public function getTYPZonePpri(): ?string
    {
        return $this->TYPZonePpri;
    }

    public function setTYPZonePpri(?string $TYPZonePpri): static
    {
        $this->TYPZonePpri = $TYPZonePpri;

        return $this;
    }

    public function getMH(): ?string
    {
        return $this->MH;
    }

    public function setMH(?string $MH): static
    {
        $this->MH = $MH;

        return $this;
    }

    public function getZoneHumide(): ?string
    {
        return $this->ZoneHumide;
    }

    public function setZoneHumide(?string $ZoneHumide): static
    {
        $this->ZoneHumide = $ZoneHumide;

        return $this;
    }

    public function getTYPInfoComp(): ?string
    {
        return $this->TYPInfoComp;
    }

    public function setTYPInfoComp(?string $TYPInfoComp): static
    {
        $this->TYPInfoComp = $TYPInfoComp;

        return $this;
    }
}
