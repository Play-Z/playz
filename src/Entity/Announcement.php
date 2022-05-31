<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\AnnouncementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AnnouncementRepository::class)
 */
class Announcement
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
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="announcements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $teamAnnouncement;

    /**
     * @ORM\ManyToOne(targetEntity=Tournament::class, inversedBy="announcements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tournamentAnnouncement;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTeamAnnouncement(): ?Team
    {
        return $this->teamAnnouncement;
    }

    public function setTeamAnnouncement(?Team $teamAnnouncement): self
    {
        $this->teamAnnouncement = $teamAnnouncement;

        return $this;
    }

    public function getTournamentAnnouncement(): ?Tournament
    {
        return $this->tournamentAnnouncement;
    }

    public function setTournamentAnnouncement(?Tournament $tournamentAnnouncement): self
    {
        $this->tournamentAnnouncement = $tournamentAnnouncement;

        return $this;
    }

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
}
