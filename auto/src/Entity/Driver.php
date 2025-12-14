<?php

namespace App\Entity;

use App\Repository\DriverRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DriverRepository::class)]
class Driver
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $fullName;

    #[ORM\Column(type: 'string', length: 16, nullable: true)]
    private ?string $licenseCategory = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $contact = null;

    #[ORM\OneToOne(inversedBy: 'driver', targetEntity: \App\Entity\User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?\App\Entity\User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getLicenseCategory(): ?string
    {
        return $this->licenseCategory;
    }

    public function setLicenseCategory(?string $licenseCategory): self
    {
        $this->licenseCategory = $licenseCategory;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getUser(): ?\App\Entity\User
    {
        return $this->user;
    }

    public function setUser(?\App\Entity\User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
