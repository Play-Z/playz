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
     * @ORM\Column(type="array")
     */
    private $players = [];

    /**
     * @ORM\ManyToMany(targetEntity=Tournament::class, mappedBy="equipes")
     */
    private $tournaments;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="tournamentTeams")
     */
    private $teams;

    public function __construct()
    {
        $this->tournaments = new ArrayCollection();
    }

    public function getPlayers(): ?array
    {
        return $this->players;
    }

    public function setPlayers(array $players): self
    {
        $this->players = $players;

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
            $tournaments->addEquipe($this);
        }

        return $this;
    }

    public function removeTournaments(Tournament $tournaments): self
    {
        if ($this->tournaments->removeElement($tournaments)) {
            $tournaments->removeEquipe($this);
        }

        return $this;
    }

    public function getTeams(): ?Team
    {
        return $this->teams;
    }

    public function setTeams(?Team $teams): self
    {
        $this->teams = $teams;

        return $this;
    }


}
