<?php

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 * @UniqueEntity(fields={"name"}, message="Il existe déjà une équipe portant ce nom dans playz.")
 */
class Team
{
    use BlameableTrait;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

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
     */
    private $users;


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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

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


}
