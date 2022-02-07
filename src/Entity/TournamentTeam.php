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
     * @ORM\ManyToMany(targetEntity=TournamentMatch::class, mappedBy="equipes")
     */
    private $tournamentMatches;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="tournamentTeams")
     */
    private $teams;

    public function __construct()
    {
        $this->tournamentMatches = new ArrayCollection();
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
            $tournamentMatch->addEquipe($this);
        }

        return $this;
    }

    public function removeTournamentMatch(TournamentMatch $tournamentMatch): self
    {
        if ($this->tournamentMatches->removeElement($tournamentMatch)) {
            $tournamentMatch->removeEquipe($this);
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
