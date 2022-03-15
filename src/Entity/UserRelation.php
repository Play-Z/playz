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
    private $status = "pending";

    /**
     * @ORM\JoinTable(name="sender_user")
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="userRelations")
     */
    private $sender;

    /**
     * @ORM\JoinTable(name="recipient_user")
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="userRelations")
     */
    private $recipient;

    public function __construct()
    {
        $this->sender = new ArrayCollection();
        $this->recipient = new ArrayCollection();
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
    public function getSender(): Collection
    {
        return $this->sender;
    }

    public function addSender(User $sender): self
    {
        if (!$this->sender->contains($sender)) {
            $this->sender[] = $sender;
        }

        return $this;
    }

    public function removeSender(User $sender): self
    {
        $this->sender->removeElement($sender);

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getRecipient(): Collection
    {
        return $this->recipient;
    }

    public function addRecipient(User $recipient): self
    {
        if (!$this->recipient->contains($recipient)) {
            $this->recipient[] = $recipient;
        }

        return $this;
    }

    public function removeRecipient(User $recipient): self
    {
        $this->recipient->removeElement($recipient);

        return $this;
    }
}
