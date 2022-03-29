<?php

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 * @UniqueEntity(fields={"name"}, message="Il existe déjà une équipe portant ce nom dans playz.")
 * @Vich\Uploadable
 */
class Team
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(
     *      min = 3,
     *      max = 50
     * )
     */
    private $name;

    /**
     * @var string|null
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $public = false;

    /**
     * @ORM\OneToMany(targetEntity=TournamentTeam::class, mappedBy="teams")
     */
    private $tournamentTeams;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="team")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $users;

    /**
     * @var User
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true, unique=true)
     */
    private $createdBy;

    /**
     * @var User|null
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="integer")
     */
    private $emplacement = 10;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

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
     * @Vich\UploadableField(mapping="teamLogo", fileNameProperty="path")
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
     * @ORM\ManyToMany(targetEntity=Game::class, inversedBy="teams")
     */
    private $games;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="teams")
     */
    private $game;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $redditUsername;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $twitchUsername;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $twitterUsername;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $discordServerToken;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $youtubeUsername;

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
        $this->tournamentTeams = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     * @return Team
     */
    public function setSlug(?string $slug): Team
    {
        $this->slug = $slug;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }

    /**
     * @return Collection|TournamentTeam[]
     */
    public function getTournamentTeams(): Collection
    {
        return $this->tournamentTeams;
    }

    public function addTournamentTeam(TournamentTeam $tournamentTeam): self
    {
        if (!$this->tournamentTeams->contains($tournamentTeam)) {
            $this->tournamentTeams[] = $tournamentTeam;
            $tournamentTeam->setTeams($this);
        }

        return $this;
    }

    public function removeTournamentTeam(TournamentTeam $tournamentTeam): self
    {
        if ($this->tournamentTeams->removeElement($tournamentTeam)) {
            // set the owning side to null (unless already changed)
            if ($tournamentTeam->getTeams() === $this) {
                $tournamentTeam->setTeams(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setTeam($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getTeam() === $this) {
                $user->setTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return User
     */
    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     * @return self
     */
    public function setCreatedBy(User $createdBy): self
    {
        if (!in_array('ROLE_ADMIN',$createdBy->getRoles())){
            $createdBy->setRoles((array('ROLE_TEAM_CREATOR')));
        }
        $createdBy->setTeam($this);
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    /**
     * @param User|null $updatedBy
     * @return self
     */
    public function setUpdatedBy(?User $updatedBy): self
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    public function getEmplacement(): ?int
    {
        return $this->emplacement;
    }

    public function setEmplacement(int $emplacement): self
    {
        $this->emplacement = $emplacement;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

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

    public function getRedditUsername(): ?string
    {
        return $this->redditUsername;
    }

    public function setRedditUsername(?string $redditUsername): self
    {
        $this->redditUsername = $redditUsername;

        return $this;
    }

    public function getTwitchUsername(): ?string
    {
        return $this->twitchUsername;
    }

    public function setTwitchUsername(?string $twitchUsername): self
    {
        $this->twitchUsername = $twitchUsername;

        return $this;
    }

    public function getTwitterUsername(): ?string
    {
        return $this->twitterUsername;
    }

    public function setTwitterUsername(?string $twitterUsername): self
    {
        $this->twitterUsername = $twitterUsername;

        return $this;
    }

    public function getDiscordServerToken(): ?string
    {
        return $this->discordServerToken;
    }

    public function setDiscordServerToken(?string $discordServerToken): self
    {
        $this->discordServerToken = $discordServerToken;

        return $this;
    }

    public function getYoutubeUsername(): ?string
    {
        return $this->youtubeUsername;
    }

    public function setYoutubeUsername(?string $youtubeUsername): self
    {
        $this->youtubeUsername = $youtubeUsername;

        return $this;
    }
}