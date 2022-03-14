<?php

namespace App\Entity;

use App\Repository\UserRelationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRelationRepository::class)
 */
class UserRelation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $status;

    /**
     * @ORM\JoinTable(name="relating_user")
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="userRelations")
     */
    private $relatingUser;

    /**
     * @ORM\JoinTable(name="related_user")
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="userRelations")
     */
    private $relatedUser;

    public function __construct()
    {
        $this->relatingUser = new ArrayCollection();
        $this->relatedUser = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getRelatingUser(): Collection
    {
        return $this->relatingUser;
    }

    public function addRelatingUser(User $relatingUser): self
    {
        if (!$this->relatingUser->contains($relatingUser)) {
            $this->relatingUser[] = $relatingUser;
        }

        return $this;
    }

    public function removeRelatingUser(User $relatingUser): self
    {
        $this->relatingUser->removeElement($relatingUser);

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getRelatedUser(): Collection
    {
        return $this->relatedUser;
    }

    public function addRelatedUser(User $relatedUser): self
    {
        if (!$this->relatedUser->contains($relatedUser)) {
            $this->relatedUser[] = $relatedUser;
        }

        return $this;
    }

    public function removeRelatedUser(User $relatedUser): self
    {
        $this->relatedUser->removeElement($relatedUser);

        return $this;
    }
}
