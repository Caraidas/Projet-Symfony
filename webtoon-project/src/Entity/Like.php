<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\LikeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
#[ApiResource]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\ManyToOne]
    private ?Webtoon $webtoon = null;

    #[ORM\Column]
    private \DateTimeImmutable $likedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?datetime
    {
        return $this->likedAt;
    }

    public function getWebtoon(): ?Webtoon
    {
        return $this->webtoon;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}

