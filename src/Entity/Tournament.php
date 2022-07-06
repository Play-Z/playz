<?php

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Repository\TournamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=TournamentRepository::class)
 * @Vich\Uploadable
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
     * @ORM\Column(type="boolean", nullable=true)
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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startInscriptionAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startAt;

    /**
    * @Gedmo\Timestampable(on="create")
    * @ORM\Column(type="datetime")
    */
    private $createdAt;

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }


    /**
     * @Vich\UploadableField(mapping="tournamentLogo", fileNameProperty="path")
     * @Assert\File(
     *     maxSize = "2M",
     *     mimeTypes = {"image/png"},
     *     mimeTypesMessage = "Please upload a valid image file (png)"
     * )
     *
     * @var File $logo
     */
    private $logo;

    /**
     * @var string|null
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Poule::class, mappedBy="tournament", orphanRemoval=true, cascade={"persist"})
     */
    private $poules;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $PouleType;

    /**
     * @ORM\OneToOne(targetEntity=Tournament::class, inversedBy="tournament", cascade={"persist", "remove"})
     */
    private $tournamentChild;

    /**
     * @ORM\OneToOne(targetEntity=Tournament::class, mappedBy="tournamentChild", cascade={"persist", "remove"})
     */
    private $tournament;

    /**
     * @ORM\OneToMany(targetEntity=Announcement::class, mappedBy="tournamentAnnouncement")
     */
    private $announcements;

    /**
     * @param UploadedFile $logo
     */
    public function setLogo(?File $logo = null)
    {
        $this->logo = $logo;
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @return UploadedFile
     */
    public function getLogo(): ?File
    {
        return $this->logo;
    }

    public function __construct()
    {
        $this->tournamentMatches = new ArrayCollection();
        $this->poules = new ArrayCollection();
        $this->equipes = new ArrayCollection();
        $this->announcements = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getStartInscriptionAt()
    {
        return $this->startInscriptionAt;
    }

    /**
     * @param mixed $startInscriptionAt
     */
    public function setStartInscriptionAt($startInscriptionAt): void
    {
        $this->startInscriptionAt = $startInscriptionAt;
    }

    /**
     * @return mixed
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * @param mixed $startAt
     */
    public function setStartAt($startAt): void
    {
        $this->startAt = $startAt;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }


    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
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

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @return Collection|Poule[]
     */
    public function getPoules(): Collection
    {
        return $this->poules;
    }

    public function addPoule(Poule $poule): self
    {
        if (!$this->poules->contains($poule)) {
            $this->poules[] = $poule;
            $poule->setTournament($this);
        }
        return $this;
    }

    /**
     *
     * @return Collection|Announcement[]
     */
    public function getAnnouncements(): Collection
    {
        return $this->announcements;
    }

    public function addAnnouncement(Announcement $announcement): self
    {
        if (!$this->announcements->contains($announcement)) {
            $this->announcements[] = $announcement;
            $announcement->setTournamentAnnouncement($this);
        }

        return $this;
    }


    public function removePoule(Poule $poule): self
    {
        if ($this->poules->removeElement($poule)) {
            // set the owning side to null (unless already changed)
            if ($poule->getTournament() === $this) {
                $poule->setTournament(null);
            }
        }

        return $this;
    }

    public function removeAnnouncement(Announcement $announcement): self
    {
        if ($this->announcements->removeElement($announcement)) {
            // set the owning side to null (unless already changed)
            if ($announcement->getTournamentAnnouncement() === $this) {
                $announcement->setTournamentAnnouncement(null);
            }
        }

        return $this;
    }


    public function getPouleType(): ?bool
    {
        return $this->PouleType;
    }

    public function setPouleType(bool $PouleType): self
    {
        $this->PouleType = $PouleType;

        return $this;
    }

public function getTournamentChild(): ?self
{
    return $this->tournamentChild;
}

public function setTournamentChild(?self $tournamentChild): self
{
    $this->tournamentChild = $tournamentChild;

    return $this;
}

public function getTournament(): ?self
{
    return $this->tournament;
}

public function setTournament(?self $tournament): self
{
    // unset the owning side of the relation if necessary
    if ($tournament === null && $this->tournament !== null) {
        $this->tournament->setTournamentChild(null);
    }

    // set the owning side of the relation if necessary
    if ($tournament !== null && $tournament->getTournamentChild() !== $this) {
        $tournament->setTournamentChild($this);
    }

    $this->tournament = $tournament;

    return $this;
}
}
