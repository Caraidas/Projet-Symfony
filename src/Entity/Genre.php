<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
#[ApiResource]
class Genre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Webtoon>
     */
    #[ORM\ManyToMany(targetEntity: Webtoon::class, mappedBy: 'genre')]
    private Collection $webtoons;

    public function __construct()
    {
        $this->webtoons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Webtoon>
     */
    public function getWebtoons(): Collection
    {
        return $this->webtoons;
    }

    public function addWebtoon(Webtoon $webtoon): static
    {
        if (!$this->webtoons->contains($webtoon)) {
            $this->webtoons->add($webtoon);
            $webtoon->addGenre($this);
        }

        return $this;
    }

    public function removeWebtoon(Webtoon $webtoon): static
    {
        if ($this->webtoons->removeElement($webtoon)) {
            $webtoon->removeGenre($this);
        }

        return $this;
    }
}
