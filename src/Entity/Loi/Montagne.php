<?php

namespace App\Entity\Loi;

use App\Repository\Loi\MontagneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MontagneRepository::class)]
class Montagne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['json'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $annee_cog = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $insee_reg_2016 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom_reg_2016 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $insee_dep = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom_dept = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['json'])]
    private ?string $insee_com = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom_com = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['json'])]
    private ?string $reglementation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnneeCog(): ?string
    {
        return $this->annee_cog;
    }

    public function setAnneeCog(?string $annee_cog): static
    {
        $this->annee_cog = $annee_cog;

        return $this;
    }

    public function getInseeReg2016(): ?string
    {
        return $this->insee_reg_2016;
    }

    public function setInseeReg2016(?string $insee_reg_2016): static
    {
        $this->insee_reg_2016 = $insee_reg_2016;

        return $this;
    }

    public function getNomReg2016(): ?string
    {
        return $this->nom_reg_2016;
    }

    public function setNomReg2016(?string $nom_reg_2016): static
    {
        $this->nom_reg_2016 = $nom_reg_2016;

        return $this;
    }

    public function getInseeDep(): ?string
    {
        return $this->insee_dep;
    }

    public function setInseeDep(?string $insee_dep): static
    {
        $this->insee_dep = $insee_dep;

        return $this;
    }

    public function getNomDept(): ?string
    {
        return $this->nom_dept;
    }

    public function setNomDept(?string $nom_dept): static
    {
        $this->nom_dept = $nom_dept;

        return $this;
    }

    public function getInseeCom(): ?string
    {
        return $this->insee_com;
    }

    public function setInseeCom(?string $insee_com): static
    {
        $this->insee_com = $insee_com;

        return $this;
    }

    public function getNomCom(): ?string
    {
        return $this->nom_com;
    }

    public function setNomCom(?string $nom_com): static
    {
        $this->nom_com = $nom_com;

        return $this;
    }

    public function getReglementation(): ?string
    {
        return $this->reglementation;
    }

    public function setReglementation(?string $reglementation): static
    {
        $this->reglementation = $reglementation;

        return $this;
    }
}
