<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CommentaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
#[ApiResource]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\ManyToOne(inversedBy: 'Commentaire')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'Commentaire')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Webtoon $webtoon = null;

    #[ORM\ManyToOne(targetEntity: Episode::class, inversedBy: 'commentaires')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Episode $episode = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getWebtoon(): ?Webtoon
    {
        return $this->webtoon;
    }

    public function setWebtoon(?Webtoon $webtoon): static
    {
        $this->webtoon = $webtoon;

        return $this;
    }

    public function getEpisode(): ?Episode
    {
        return $this->episode;
    }

    public function setEpisode(?Episode $episode): static
    {
        $this->episode = $episode;

        return $this;
    }
}
