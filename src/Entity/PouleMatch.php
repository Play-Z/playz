<?php

namespace App\Entity;

use App\Repository\PouleMatchRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PouleMatchRepository::class)
 */
class PouleMatch
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Poule::class, inversedBy="pouleMatches")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Poule;

    /**
     * @ORM\ManyToOne(targetEntity=PouleEquipe::class, inversedBy="pouleMatches")
     * @ORM\JoinColumn(nullable=false)
     */
    private $EquipeUne;

    /**
     * @ORM\ManyToOne(targetEntity=PouleEquipe::class, inversedBy="pouleMatches")
     * @ORM\JoinColumn(nullable=false)
     */
    private $EquipeDeux;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $FirstTeamWin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoule(): ?Poule
    {
        return $this->Poule;
    }

    public function setPoule(?Poule $Poule): self
    {
        $this->Poule = $Poule;

        return $this;
    }

    public function getEquipeUne(): ?PouleEquipe
    {
        return $this->EquipeUne;
    }

    public function setEquipeUne(?PouleEquipe $EquipeUne): self
    {
        $this->EquipeUne = $EquipeUne;

        return $this;
    }

    public function getEquipeDeux(): ?PouleEquipe
    {
        return $this->EquipeDeux;
    }

    public function setEquipeDeux(?PouleEquipe $EquipeDeux): self
    {
        $this->EquipeDeux = $EquipeDeux;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getFirstTeamWin(): ?bool
    {
        return $this->FirstTeamWin;
    }

    public function setFirstTeamWin(?bool $FirstTeamWin): self
    {
        $this->FirstTeamWin = $FirstTeamWin;

        return $this;
    }
}
