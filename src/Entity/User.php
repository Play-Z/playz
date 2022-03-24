<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"email"}, message="Il existe déjà un compte playz lié à cette adresse email.")
 * @Vich\Uploadable
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string
     * @ORM\Column(type="string", length=40)
     */
    private string $username;

    /**
     * @var string|null
     * @Gedmo\Slug(fields={"username"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $timezone;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $organization;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $organization_position;

    /**
     * @ORM\Column(type="boolean")
     */
    private $newsletter = false;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="users")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $team;

    /**
     * @ORM\OneToMany(targetEntity=UserRelation::class, mappedBy="sender")
     */
    private $sendedUserRelations;

    /**
     * @ORM\OneToMany(targetEntity=UserRelation::class, mappedBy="recipient")
     */
    private $receivedUserRelations;

    /**
     * @var \DateTime $createdAt
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime $updatedAt
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     *
     */
    private $updatedAt;

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

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
     * @Vich\UploadableField(mapping="userImage", fileNameProperty="path")
     *
     * @var File $image
     *
     * @Assert\File(
     *     maxSize = "2M",
     *     mimeTypes = {"image/png", "image/jpeg"},
     *     mimeTypesMessage = "Please upload a valid image file (png, jpg/jpeg)"
     * )
     */
    private $image;

    /**
     * @param UploadedFile $image
     */
    public function setImage(?File $image = null)
    {
        $this->image = $image;
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @return UploadedFile
     */
    public function getImage(): ?File
    {
        return $this->image;
    }

    public function __construct()
    {
        $this->sendedUserRelations = new ArrayCollection();
        $this->receivedUserRelations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
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
     * @return User
     */
    public function setSlug(?string $slug): User
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getOrganization(): ?string
    {
        return $this->organization;
    }

    public function setOrganization(?string $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function getOrganizationPosition(): ?string
    {
        return $this->organization_position;
    }

    public function setOrganizationPosition(?string $organization_position): self
    {
        $this->organization_position = $organization_position;

        return $this;
    }

    public function getNewsletter(): ?bool
    {
        return $this->newsletter;
    }

    public function setNewsletter(bool $newsletter): self
    {
        $this->newsletter = $newsletter;

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
     * @return Collection|UserRelation[]
     */
    public function getSendedUserRelations(): Collection
    {
        return $this->sendedUserRelations;
    }

    public function addSendedUserRelation(UserRelation $sendedUserRelation): self
    {
        if (!$this->sendedUserRelations->contains($sendedUserRelation)) {
            $this->sendedUserRelations[] = $sendedUserRelation;
            $sendedUserRelation->setSender($this);
        }

        return $this;
    }

    public function removeSendedUserRelation(UserRelation $sendedUserRelation): self
    {
        if ($this->sendedUserRelations->removeElement($sendedUserRelation)) {
            // set the owning side to null (unless already changed)
            if ($sendedUserRelation->getSender() === $this) {
                $sendedUserRelation->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserRelation[]
     */
    public function getReceivedUserRelations(): Collection
    {
        return $this->receivedUserRelations;
    }

    public function addReceivedUserRelation(UserRelation $receivedUserRelation): self
    {
        if (!$this->receivedUserRelations->contains($receivedUserRelation)) {
            $this->receivedUserRelations[] = $receivedUserRelation;
            $receivedUserRelation->setRecipient($this);
        }

        return $this;
    }

    public function removeReceivedUserRelation(UserRelation $receivedUserRelation): self
    {
        if ($this->receivedUserRelations->removeElement($receivedUserRelation)) {
            // set the owning side to null (unless already changed)
            if ($receivedUserRelation->getRecipient() === $this) {
                $receivedUserRelation->setRecipient(null);
            }
        }

        return $this;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password,
            ) = unserialize($serialized);
    }
}
