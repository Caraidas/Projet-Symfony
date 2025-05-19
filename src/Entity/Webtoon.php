<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\WebtoonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WebtoonRepository::class)]
#[ApiResource]
class Webtoon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'auteur')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    /**
     * @var Collection<int, Genre>
     */
    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: 'webtoons')]
    private Collection $genre;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'webtoon', orphanRemoval: true)]
    private Collection $Commentaire;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'likes')]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'webtoon', targetEntity: Like::class, orphanRemoval: true)]
    private Collection $likes;

    /**
     * @var Collection<int, Episode>
     */
    #[ORM\OneToMany(mappedBy: 'webtoon', targetEntity: Episode::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $episodes;

    public function __construct()
    {
        $this->genre = new ArrayCollection();
        $this->Commentaire = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->episodes = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }

    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addEpisode(Episode $episode): static
    {
        if (!$this->episodes->contains($episode)) {
            $this->episodes[] = $episode;
            $episode->setWebtoon($this);
        }

        return $this;
    }

    public function removeEpisode(Episode $episode): static
    {
        if ($this->episodes->removeElement($episode)) {
            if ($episode->getWebtoon() === $this) {
                $episode->setWebtoon(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Genre>
     */
    public function getGenre(): Collection
    {
        return $this->genre;
    }

    public function setGenres(Collection $genres): self
    {
        $this->genres = $genres;
        return $this;
    }


    public function addGenre(Genre $genre): static
    {
        if (!$this->genre->contains($genre)) {
            $this->genre->add($genre);
        }

        return $this;
    }

    public function removeGenre(Genre $genre): static
    {
        $this->genre->removeElement($genre);

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
            $commentaire->setWebtoon($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->Commentaire->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getWebtoon() === $this) {
                $commentaire->setWebtoon(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addLike($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeLike($this);
        }

        return $this;
    }
}
