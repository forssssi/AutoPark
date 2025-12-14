<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 64)]
    private string $vin;

    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    #[Assert\Length(max: 32)]
    private ?string $plateNumber = null;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    #[Assert\Length(max: 128)]
    private ?string $model = null;

    #[ORM\Column(type: 'string', length: 32)]
    #[Assert\Choice(choices: ['active','inactive','maintenance'])]
    private string $status = 'active';

    #[ORM\Column(type: 'integer')]
    #[Assert\PositiveOrZero]
    private int $mileage = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVin(): string
    {
        return $this->vin;
    }

    public function setVin(string $vin): self
    {
        $this->vin = $vin;

        return $this;
    }

    public function getPlateNumber(): ?string
    {
        return $this->plateNumber;
    }

    public function setPlateNumber(?string $plateNumber): self
    {
        $this->plateNumber = $plateNumber;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getMileage(): int
    {
        return $this->mileage;
    }

    public function setMileage(int $mileage): self
    {
        $this->mileage = $mileage;

        return $this;
    }
}
