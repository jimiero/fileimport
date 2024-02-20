<?php

namespace App\Entity;

use App\Repository\PersonaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: PersonaRepository::class)]
#[Broadcast]
class Persona
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $NomDuGroupe = null;

    #[ORM\Column(length: 255)]
    private ?string $Origine = null;

    #[ORM\Column(length: 255)]
    private ?string $Ville = null;

    #[ORM\Column(length: 255)]
    private ?string $AnneeDebut = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $AnneeSeparation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Fodateurs = null;

    #[ORM\Column(nullable: true)]
    private ?int $Members = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $CourantMusical = null;

    #[ORM\Column(length: 255)]
    private ?string $Presentation = null;

    #[ORM\ManyToOne(targetEntity: Deposer::class, inversedBy: 'personas')]
    #[ORM\JoinColumn('deposer_id')]

    private Deposer $deposer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomDuGroupe(): ?string
    {
        return $this->NomDuGroupe;
    }

    public function setNomDuGroupe(string $NomDuGroupe): static
    {
        $this->NomDuGroupe = $NomDuGroupe;

        return $this;
    }

    public function getOrigine(): ?string
    {
        return $this->Origine;
    }

    public function setOrigine(string $Origine): static
    {
        $this->Origine = $Origine;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->Ville;
    }

    public function setVille(string $Ville): static
    {
        $this->Ville = $Ville;

        return $this;
    }

    public function getAnneeDebut(): ?string
    {
        return $this->AnneeDebut;
    }

    public function setAnneeDebut(string $AnneeDebut): static
    {
        $this->AnneeDebut = $AnneeDebut;

        return $this;
    }

    public function getAnneeSeparation(): ?string
    {
        return $this->AnneeSeparation;
    }

    public function setAnneeSeparation(?string $AnneeSeparation): static
    {
        $this->AnneeSeparation = $AnneeSeparation;

        return $this;
    }

    public function getFodateurs(): ?string
    {
        return $this->Fodateurs;
    }

    public function setFodateurs(?string $Fodateurs): static
    {
        $this->Fodateurs = $Fodateurs;

        return $this;
    }

    public function getMembers(): ?int
    {
        return $this->Members;
    }

    public function setMembers(?int $Members): static
    {
        $this->Members = $Members;

        return $this;
    }

    public function getCourantMusical(): ?string
    {
        return $this->CourantMusical;
    }

    public function setCourantMusical(?string $CourantMusical): static
    {
        $this->CourantMusical = $CourantMusical;

        return $this;
    }

    public function getPresentation(): ?string
    {
        return $this->Presentation;
    }

    public function setPresentation(string $Presentation): static
    {
        $this->Presentation = $Presentation;

        return $this;
    }

    public function getDeposer(): Deposer
    {
        return $this->deposer;
    }

    public function setDeposer($deposer): self
    {
        $this->deposer = $deposer;

        return $this;
    }

}
