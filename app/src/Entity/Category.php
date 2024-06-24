<?php
/**
 * Category entity.
 */

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category class representing a category entity in the application.
 */
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    // Unique identifier for the category.
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Title of the category.
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    // Timestamp for when the category was created.
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Timestamp for when the category was last updated.
    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    // Slug (URL-friendly version of the title) for the category.
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $slug = null;

    /**
     * @var Collection<int, Song> collection of songs associated with this category
     */
    #[ORM\OneToMany(targetEntity: Song::class, mappedBy: 'category', fetch: 'EXTRA_LAZY')]
    private Collection $songs;

    /**
     * koństruktor.
     */
    public function __construct()
    {
        $this->songs = new ArrayCollection();
    }

    /**
     * Getter for Name.
     *
     * @return string|null Name
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null zwaraca
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title param
     *
     * @return $this return
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null return
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeImmutable $createdAt param
     *
     * @return $this return
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null return
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeImmutable $updatedAt param
     *
     * @return $this return
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return string|null return
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug param
     *
     * @return $this return
     */
    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Song> return
     */
    public function getSongs(): Collection
    {
        return $this->songs;
    }
}
