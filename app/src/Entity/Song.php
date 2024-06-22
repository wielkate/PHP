<?php

namespace App\Entity;

use App\Repository\SongRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SongRepository::class)]
class Song
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'songs', fetch: 'EXTRA_LAZY')]
    private Collection $tags;

    #[ORM\ManyToOne( fetch: 'EXTRA_LAZY', inversedBy: 'songs')]
    private ?Category $category = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?DateTimeInterface $duration = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $comment = null;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
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

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTag(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tags): static
    {
        if (!$this->tags->contains($tags)) {
            $this->tags->add($tags);
        }

        return $this;
    }

    public function removeTag(Tag $tags): static
    {
        $this->tags->removeElement($tags);

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getDuration(): ?DateTimeInterface
    {
        return $this->duration;
    }

    public function setDuration(DateTimeInterface $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getFormattedUpdatedAt(): string
    {
        return $this->updatedAt->format('Y\m\d');
    }

    public function getFormattedCreatedAt(): string
    {
        return $this->createdAt->format('Y\m\d');
    }
}
