<?php

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Repository\TournamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TournamentRepository::class)
 */
class Tournament
{
    use BlameableTrait ;
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
     * @ORM\Column(type="smallint")
     */
    private $max_team_participant;

    /**
     * @ORM\Column(type="smallint")
     */
    private $max_team_players;

    /**
     * @ORM\ManyToOne(targetEntity=Price::class, inversedBy="tounrmanet")
     */
    private $price;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity=TournamentTeam::class, inversedBy="tournaments")
     */
    private $equipes;
    /**
     * @ORM\OneToMany(targetEntity=TournamentMatch::class, mappedBy="tournaments")
     */
    private $tournamentMatches;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="tournaments")
     */
    private $game;




    public function __construct()
    {
        $this->tournamentMatches = new ArrayCollection();
        $this->setStatus(false) ;

    }

    /**
     * @return Collection|TournamentTeam[]
     */
    public function getEquipes(): Collection
    {
        return $this->equipes;
    }

    public function addEquipes(TournamentTeam $equipes): self
    {
        if (!$this->equipes->contains($equipes)) {
            $this->equipes[] = $equipes;
        }

        return $this;
    }

    public function removeEquipes(TournamentTeam $equipes): self
    {
        $this->equipes->removeElement($equipes);

        return $this;
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

    public function getMaxTeamParticipant(): ?int
    {
        return $this->max_team_participant;
    }

    public function setMaxTeamParticipant(int $max_team_participant): self
    {
        $this->max_team_participant = $max_team_participant;

        return $this;
    }

    public function getMaxTeamPlayers(): ?int
    {
        return $this->max_team_players;
    }

    public function setMaxTeamPlayers(int $max_team_players): self
    {
        $this->max_team_players = $max_team_players;

        return $this;
    }


    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(?Price $price): self
    {
        $this->price = $price;

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
            $tournamentMatch->setTournaments($this);
        }

        return $this;
    }

    public function removeTournamentMatch(TournamentMatch $tournamentMatch): self
    {
        if ($this->tournamentMatches->removeElement($tournamentMatch)) {
            // set the owning side to null (unless already changed)
            if ($tournamentMatch->getTournaments() === $this) {
                $tournamentMatch->setTournaments(null);
            }
        }

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }


}
