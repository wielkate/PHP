<?php
/**
 * Category entity.
 */

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Category class representing a category entity in the application.
 */
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 64)]
    private ?string $title = null;

    #[Assert\Type(\DateTimeImmutable::class)]
    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    #[Assert\Type(\DateTimeImmutable::class)]
    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[Assert\Type('string')]
    #[Assert\Length(min: 3, max: 64)]
    #[ORM\Column(length: 64, nullable: true)]
    #[Gedmo\Slug(fields: ['title'])]
    private ?string $slug = null;

    /**
     * @var Collection<int, Song> collection of songs associated with this category
     */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Song::class, fetch: 'EXTRA_LAZY')]
    private Collection $songs;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->songs = new ArrayCollection();
    }

    /**
     * Getter for Name.
     *
     * @return int|null Name
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null title
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
