<?php

namespace App\Entity;

use App\Repository\TournamentMatchRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TournamentMatchRepository::class)
 */
class TournamentMatch
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;


    /**
     * @ORM\Column(type="boolean",  nullable=true)
     */
    private $team_one_win;

    /**
     * @ORM\ManyToOne(targetEntity=Tournament::class, inversedBy="tournamentMatches",cascade={"persist"})
     */
    private $tournaments;

    /**
     * @ORM\ManyToOne(targetEntity=TournamentMatch::class, inversedBy="match_enfants",cascade={"persist"})
     */
    private $match_parent;

    /**
     * @ORM\OneToMany(targetEntity=TournamentMatch::class, mappedBy="match_parent")
     */
    private $match_enfants;

    /**
     * @ORM\ManyToOne(targetEntity=TournamentTeam::class, inversedBy="tournamentMatches")
     */
    private $team_one;

    /**
     * @ORM\ManyToOne(targetEntity=TournamentTeam::class, inversedBy="tournamentMatches")
     */
    private $team_two;


    public function __construct(String $name, Tournament $tournament, TournamentMatch $match_parent = null)
    {
        $this->equipes = new ArrayCollection();
        $this->name = $name ;
        $this->setTournaments($tournament) ;
        $this->match_enfants = new ArrayCollection();
        if($match_parent != null)
            $this->match_parent = $match_parent ;
        $this->status = false ;
    }

    public function getId(): ?int
    {
        return $this->id;
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



    public function getTournaments(): ?Tournament
    {
        return $this->tournaments;
    }

    public function setTournaments(?Tournament $tournaments): self
    {
        $this->tournaments = $tournaments;

        return $this;
    }

    public function getMatchParent(): ?self
    {
        return $this->match_parent;
    }

    public function setMatchParent(?self $match_parent): self
    {
        $this->match_parent = $match_parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getMatchEnfants(): Collection
    {
        return $this->match_enfants;
    }

    public function addMatchEnfant(self $matchEnfant): self
    {
        if (!$this->match_enfants->contains($matchEnfant)) {
            $this->match_enfants[] = $matchEnfant;
            $matchEnfant->setMatchParent($this);
        }

        return $this;
    }

    public function removeMatchEnfant(self $matchEnfant): self
    {
        if ($this->match_enfants->removeElement($matchEnfant)) {
            // set the owning side to null (unless already changed)
            if ($matchEnfant->getMatchParent() === $this) {
                $matchEnfant->setMatchParent(null);
            }
        }

        return $this;
    }

    public function getTeamOne(): ?TournamentTeam
    {
        return $this->team_one;
    }

    public function setTeamOne(?TournamentTeam $team_one): self
    {
        $this->team_one = $team_one;

        return $this;
    }

    public function getTeamTwo(): ?TournamentTeam
    {
        return $this->team_two;
    }

    public function setTeamTwo(?TournamentTeam $team_two): self
    {
        $this->team_two = $team_two;

        return $this;
    }

    /**
     * @return false
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param false $status
     */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getTeamOneWin()
    {
        return $this->team_one_win;
    }

    /**
     * @param mixed $team_one_win
     */
    public function setTeamOneWin($team_one_win): void
    {
        $this->team_one_win = $team_one_win;
    }


}
