<?php

namespace App\Entity;

use App\Repository\RelaisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RelaisRepository::class)]
class Relais
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commune = null;

    #[ORM\Column(length: 255)]
    private ?string $lat = null;

    #[ORM\Column(length: 255)]
    private ?string $lon = null;

    #[ORM\OneToMany(targetEntity: RecordAirtable::class, mappedBy: 'Relais')]
    private Collection $recordAirtables;

    public function __construct()
    {
        $this->recordAirtables = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCommune(): ?string
    {
        return $this->commune;
    }

    public function setCommune(?string $commune): static
    {
        $this->commune = $commune;

        return $this;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(string $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLon(): ?string
    {
        return $this->lon;
    }

    public function setLon(string $lon): static
    {
        $this->lon = $lon;

        return $this;
    }

    /**
     * @return Collection<int, RecordAirtable>
     */
    public function getRecordAirtables(): Collection
    {
        return $this->recordAirtables;
    }

    public function addRecordAirtable(RecordAirtable $recordAirtable): static
    {
        if (!$this->recordAirtables->contains($recordAirtable)) {
            $this->recordAirtables->add($recordAirtable);
            $recordAirtable->setRelais($this);
        }

        return $this;
    }

    public function removeRecordAirtable(RecordAirtable $recordAirtable): static
    {
        if ($this->recordAirtables->removeElement($recordAirtable)) {
            // set the owning side to null (unless already changed)
            if ($recordAirtable->getRelais() === $this) {
                $recordAirtable->setRelais(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return '[' . $this->commune . '] ' . $this->nom; 
    }
}
