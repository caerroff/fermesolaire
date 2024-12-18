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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Relais = null;

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

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $MH = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $ZoneHumide = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $TYPInfoComp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $recordId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Biotope = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ParcNationaux = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $longitude = null;

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

    public function getRelais(): ?string
    {
        return $this->Relais;
    }

    public function setRelais(?string $Relais): static
    {
        $this->Relais = $Relais;

        return $this;
    }

    public function setRecord(array $record)
    {
        ksort($record['fields']);
        $this->setLatitude($record['fields']['Latitude'] ?? null);
        $this->setLongitude($record['fields']['Longitude'] ?? null);
        $this->setRecordId($record['id']);
        $this->setTYPUrba($record['fields']['TYP: Urba'] ?? null);
        $this->setRPG($record['fields']['RPG 2023 intranet'] ?? null);
        $this->setTYPDisRacc($record['fields']['TYP: DistRacc'] ?? null);
        $this->setTYPCapRacc($record['fields']['TYP: CapaRacc'] ?? null);
        $this->setTYPNomRacc($record['fields']['TYP: NomRacc'] ?? null);
        $this->setTYPVilleRacc($record['fields']['TYP: VilleRacc'] ?? null);
        $this->setTYPUrba($record['fields']['TYP: Urba'] ?? null);
        $this->setTYPEnviro($record['fields']['TYP: Enviro'] ?? null);
        $this->setZNIEFF1($record['fields']['ZNIEFF 1 -10 km'] ?? null);
        $this->setZNIEFF2($record['fields']['ZNIEFF 2 -10 km'] ?? null);
        $this->setN2000Habitats($record['fields']['N 2000 - DHabitats -10 km'] ?? null);
        $this->setBiotope($record['fields']['Biotope 1-10 km'] ?? null);
        $this->setParcNationaux($record['fields']['Parc National 1-10 km'] ?? null);
        $this->setN2000DOiseaux($record['fields']['N2000 - DOiseaux -10 km'] ?? null);
        $this->setZoneHumide($record['fields']['Zone humide'] ?? null);
        $this->setMH($record['fields']['MH'] ?? null);
        $this->setTYPInfoComp($record['fields']['TYP: InfoComp'] ?? null);
        $this->setTYPPpri($record['fields']['TYP: PPRi'] ?? null);
        $this->setTYPZonePpri($record['fields']['TYP : Zone PPRi'] ?? null);
        $this->setTYPGhi($record['fields']['TYP: GHI'] ?? null);
        $this->setPNR($record['fields']['PNR -10 km'] ?? null);
    }

    public function getRecord()
    {
        $json = [];
        $json['id'] = $this->getRecordId() ?? null;
        $json['fields']['TYP: Urba'] = $this->getTYPUrba() ?? null;
        if ($this->getRPG() != []) {
            $json['fields']['RPG 2023 intranet'] = $this->getRPG() ?? null;
        }
        $json['fields']['TYP: DistRacc'] = (float) $this->getTYPDisRacc() ?? null;
        $json['fields']['Latitude'] = $this->getLatitude() ?? null;
        $json['fields']['Longitude'] = $this->getLongitude() ?? null;
        $json['fields']['TYP: CapaRacc'] = $this->getTYPCapRacc() ?? null;
        $json['fields']['TYP: NomRacc'] = $this->getTYPNomRacc() ?? null;
        $json['fields']['TYP: VilleRacc'] = $this->getTYPVilleRacc() ?? null;
        $json['fields']['TYP: Urba'] = $this->getTYPUrba() ?? null;
        if ($this->getTYPEnviro() != []) {
            $json['fields']['TYP: Enviro'] = $this->getTYPEnviro() ?? null;
        }
        $json['fields']['ZNIEFF 1 -10 km'] = $this->getZNIEFF1() ?? null;
        $json['fields']['ZNIEFF 2 -10 km'] = $this->getZNIEFF2() ?? null;
        $json['fields']['N 2000 - DHabitats -10 km'] = $this->getN2000Habitats() ?? null;
        $json['fields']['N2000 - DOiseaux -10 km'] = $this->getN2000DOiseaux() ?? null;
        $json['fields']['Biotope 1-10 km'] = $this->getBiotope() ?? null;
        $json['fields']['Parc National 1-10 km'] = $this->getParcNationaux() ?? null;
        $json['fields']['Zone humide'] = $this->getZoneHumide() ?? null;
        $json['fields']['MH'] = $this->getMH() ?? null;
        $json['fields']['TYP: InfoComp'] = $this->getTYPInfoComp() ?? null;
        $json['fields']['TYP: PPRi'] = $this->getTYPPpri() ?? null;
        $json['fields']['TYP : Zone PPRi'] = $this->getTYPZonePpri() ?? null;
        $json['fields']['TYP: GHI'] = $this->getTYPGhi() ?? null;
        $json['fields']['PNR -10 km'] = $this->getPNR() ?? null;
        return $json;
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

    public function getMH(): ?array
    {
        return $this->MH;
    }

    public function setMH(?array $MH): static
    {
        $this->MH = $MH;

        return $this;
    }

    public function getZoneHumide(): ?array
    {
        return $this->ZoneHumide;
    }

    public function setZoneHumide(?array $ZoneHumide): static
    {
        $this->ZoneHumide = $ZoneHumide;

        return $this;
    }

    public function addZoneHumide(?string $value): static
    {
        if (in_array($value, $this->ZoneHumide)) {
            return $this;
        }
        $this->ZoneHumide[] = $value;

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

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function setRecordId(?string $recordId): static
    {
        $this->recordId = $recordId;

        return $this;
    }

    public function getBiotope(): ?string
    {
        return $this->Biotope;
    }

    public function setBiotope(?string $Biotope): static
    {
        $this->Biotope = $Biotope;

        return $this;
    }

    public function getParcNationaux(): ?string
    {
        return $this->ParcNationaux;
    }

    public function setParcNationaux(?string $ParcNationaux): static
    {
        $this->ParcNationaux = $ParcNationaux;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }
}
