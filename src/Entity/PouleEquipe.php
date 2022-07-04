<?php

namespace App\Entity;

use App\Repository\PouleEquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PouleEquipeRepository::class)
 */
class PouleEquipe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Poule::class, inversedBy="pouleEquipes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $poule;

    /**
     * @ORM\ManyToOne(targetEntity=TournamentTeam::class, inversedBy="pouleEquipes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $TournamentTeam;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nombreVictoire;

    /**
     * @ORM\OneToMany(targetEntity=PouleMatch::class, mappedBy="EquipeUne", orphanRemoval=true)
     */
    private $pouleMatches;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $qualified;

    public function __construct()
    {
        $this->pouleMatches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoule(): ?Poule
    {
        return $this->poule;
    }

    public function setPoule(?Poule $poule): self
    {
        $this->poule = $poule;

        return $this;
    }

    public function getTournamentTeam(): ?TournamentTeam
    {
        return $this->TournamentTeam;
    }

    public function setTournamentTeam(?TournamentTeam $TournamentTeam): self
    {
        $this->TournamentTeam = $TournamentTeam;

        return $this;
    }

    public function getNombreVictoire(): ?int
    {
        return $this->nombreVictoire;
    }

    public function setNombreVictoire(?int $nombreVictoire): self
    {
        $this->nombreVictoire = $nombreVictoire;

        return $this;
    }

    /**
     * @return Collection|PouleMatch[]
     */
    public function getPouleMatches(): Collection
    {
        return $this->pouleMatches;
    }

    public function addPouleMatch(PouleMatch $pouleMatch): self
    {
        if (!$this->pouleMatches->contains($pouleMatch)) {
            $this->pouleMatches[] = $pouleMatch;
            $pouleMatch->setEquipeUne($this);
        }

        return $this;
    }

    public function removePouleMatch(PouleMatch $pouleMatch): self
    {
        if ($this->pouleMatches->removeElement($pouleMatch)) {
            // set the owning side to null (unless already changed)
            if ($pouleMatch->getEquipeUne() === $this) {
                $pouleMatch->setEquipeUne(null);
            }
        }

        return $this;
    }

    public function getQualified(): ?bool
    {
        return $this->qualified;
    }

    public function setQualified(?bool $qualified): self
    {
        $this->qualified = $qualified;

        return $this;
    }
}
