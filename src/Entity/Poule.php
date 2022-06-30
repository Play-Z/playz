<?php

namespace App\Entity;

use App\Repository\PouleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PouleRepository::class)
 */
class Poule
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Tournament::class, inversedBy="poules", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $tournament;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=PouleEquipe::class, mappedBy="poule", orphanRemoval=true,cascade={"persist"})
     */
    private $pouleEquipes;

    /**
     * @ORM\OneToMany(targetEntity=PouleMatch::class, mappedBy="Poule", orphanRemoval=true,cascade={"persist"})
     */
    private $pouleMatches;

    public function __construct()
    {
        $this->pouleEquipes = new ArrayCollection();
        $this->pouleMatches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTournament(): ?Tournament
    {
        return $this->tournament;
    }

    public function setTournament(?Tournament $tournament): self
    {
        $this->tournament = $tournament;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|PouleEquipe[]
     */
    public function getPouleEquipes(): Collection
    {
        return $this->pouleEquipes;
    }

    public function addPouleEquipe(PouleEquipe $pouleEquipe): self
    {
        if (!$this->pouleEquipes->contains($pouleEquipe)) {
            $this->pouleEquipes[] = $pouleEquipe;
            $pouleEquipe->setPoule($this);
        }

        return $this;
    }

    public function removePouleEquipe(PouleEquipe $pouleEquipe): self
    {
        if ($this->pouleEquipes->removeElement($pouleEquipe)) {
            // set the owning side to null (unless already changed)
            if ($pouleEquipe->getPoule() === $this) {
                $pouleEquipe->setPoule(null);
            }
        }

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
            $pouleMatch->setPoule($this);
        }

        return $this;
    }

    public function removePouleMatch(PouleMatch $pouleMatch): self
    {
        if ($this->pouleMatches->removeElement($pouleMatch)) {
            // set the owning side to null (unless already changed)
            if ($pouleMatch->getPoule() === $this) {
                $pouleMatch->setPoule(null);
            }
        }

        return $this;
    }
}
