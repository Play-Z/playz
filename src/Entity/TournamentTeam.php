<?php

namespace App\Entity;

use App\Repository\TournamentTeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TournamentTeamRepository::class)
 */
class TournamentTeam
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="tournamentTeams")
     */
    private $players ;

    /**
     * @ORM\ManyToMany(targetEntity=Tournament::class, mappedBy="equipes")
     */
    private $tournaments;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="tournamentTeam")
     */
    private $team;

    /**
     * @ORM\OneToMany(targetEntity=TournamentMatch::class, mappedBy="team_one")
     */
    private $tournamentMatches;

    /**
     * @ORM\OneToMany(targetEntity=PouleEquipe::class, mappedBy="TournamentTeam", orphanRemoval=true)
     */
    private $pouleEquipes;

    public function __construct()
    {
        $this->tournaments = new ArrayCollection();
        $this->players = new ArrayCollection() ;
        $this->tournamentMatches = new ArrayCollection();
        $this->pouleEquipes = new ArrayCollection();
    }

    public function getPlayers()
    {
        return $this->players;
    }

    public function addPlayers(User $players): self
    {
        if (!$this->players->contains($players)) {
            $this->players[] = $players;
            $players->addTournamentTeams($this);
        }

        return $this;
    }

    public function removePlayers(User $players): self
    {
        if ($this->players->removeElement($players)) {
            $players->removeTournamentTeams($this);
        }

        return $this;
    }


    public function getTournaments(): Collection
    {
        return $this->tournaments;
    }

    public function addTournaments(Tournament $tournaments): self
    {
        if (!$this->tournaments->contains($tournaments)) {
            $this->tournaments[] = $tournaments;
            $tournaments->addEquipes($this);
        }

        return $this;
    }

    public function removeTournaments(Tournament $tournaments): self
    {
        if ($this->tournaments->removeElement($tournaments)) {
            $tournaments->removeEquipes($this);
        }

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    /**
     * @return Collection|TournamentMatch[]
     */
    public function getTournamentMatches(): Collection
    {
        return $this->tournamentMatches;
    }

    public function addTournamentMatch(TournamentMatch $tournamentMatch): self
    {
        if (!$this->tournamentMatches->contains($tournamentMatch)) {
            $this->tournamentMatches[] = $tournamentMatch;
            $tournamentMatch->setTeamOne($this);
        }

        return $this;
    }

    public function removeTournamentMatch(TournamentMatch $tournamentMatch): self
    {
        if ($this->tournamentMatches->removeElement($tournamentMatch)) {
            // set the owning side to null (unless already changed)
            if ($tournamentMatch->getTeamOne() === $this) {
                $tournamentMatch->setTeamOne(null);
            }
        }

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
            $pouleEquipe->setTournamentTeam($this);
        }

        return $this;
    }

    public function removePouleEquipe(PouleEquipe $pouleEquipe): self
    {
        if ($this->pouleEquipes->removeElement($pouleEquipe)) {
            // set the owning side to null (unless already changed)
            if ($pouleEquipe->getTournamentTeam() === $this) {
                $pouleEquipe->setTournamentTeam(null);
            }
        }

        return $this;
    }


}
