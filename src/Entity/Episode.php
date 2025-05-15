<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\EpisodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Webtoon;
use App\Entity\Image;

#[ORM\Entity(repositoryClass: EpisodeRepository::class)]
#[ApiResource]
class Episode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?int $number = null;

    #[ORM\ManyToOne(targetEntity: Webtoon::class, inversedBy: 'episodes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Webtoon $webtoon = null;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'episode', orphanRemoval: true)]
    private Collection $Commentaire;

    public function __construct()
    {
        $this->Commentaire = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(mappedBy: 'episode', targetEntity: Image::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $images;

    public function getWebtoon(): ?Webtoon
    {
        return $this->webtoon;
    }

    public function setWebtoon(?Webtoon $webtoon): static
    {
        $this->webtoon = $webtoon;

        return $this;
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setEpisode($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getEpisode() === $this) {
                $image->setEpisode(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaire(): Collection
    {
        return $this->Commentaire;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->Commentaire->contains($commentaire)) {
            $this->Commentaire->add($commentaire);
            $commentaire->setEpisode($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->Commentaire->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getEpisode() === $this) {
                $commentaire->setEpisode(null);
            }
        }

        return $this;
    }
}
