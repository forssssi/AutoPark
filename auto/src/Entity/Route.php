<?php

namespace App\Entity;

use App\Repository\RouteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RouteRepository::class)]
class Route
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $startLocation;

    #[ORM\Column(type: 'string', length: 255)]
    private string $endLocation;

    #[ORM\Column(type: 'float')]
    private float $distance = 0.0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartLocation(): string
    {
        return $this->startLocation;
    }

    public function setStartLocation(string $startLocation): self
    {
        $this->startLocation = $startLocation;

        return $this;
    }

    public function getEndLocation(): string
    {
        return $this->endLocation;
    }

    public function setEndLocation(string $endLocation): self
    {
        $this->endLocation = $endLocation;

        return $this;
    }

    public function getDistance(): float
    {
        return $this->distance;
    }

    public function setDistance(float $distance): self
    {
        $this->distance = $distance;

        return $this;
    }
}
