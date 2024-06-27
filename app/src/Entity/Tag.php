<?php
/**
 * Tag entity.
 */

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Tag class representing a tag entity in the application.
 */
#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
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

    /**
     * @var \DateTimeImmutable|null timestamp for when the tag was created
     */
    #[Assert\Type(\DateTimeImmutable::class)]
    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var \DateTimeImmutable|null timestamp for when the tag was last updated
     */
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
     * @var Collection<int, Song> collection of songs associated with this tag
     */
    #[ORM\ManyToMany(targetEntity: Song::class, mappedBy: 'tags', fetch: 'EXTRA_LAZY')]
    private Collection $songs;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->songs = new ArrayCollection();
    }

    /**
     * Get the unique identifier for the tag.
     *
     * @return int|null return
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the title of the tag.
     *
     * @return string|null return
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the title of the tag.
     *
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
     * Get the creation timestamp of the tag.
     *
     * @return \DateTimeImmutable|null return
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Set the creation timestamp of the tag.
     *
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
     * Get the last updated timestamp of the tag.
     *
     * @return \DateTimeImmutable|null return
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Set the last updated timestamp of the tag.
     *
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
     * Get the slug of the tag.
     *
     * @return string|null return
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set the slug of the tag.
     *
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
     * Get the collection of songs associated with this tag.
     *
     * @return Collection<int, Song>
     */
    public function getSongs(): Collection
    {
        return $this->songs;
    }

    /**
     * Add a song to the tag.
     *
     * @param Song $song param
     *
     * @return $this return
     */
    public function addSong(Song $song): static
    {
        if (!$this->songs->contains($song)) {
            $this->songs->add($song);
            $song->addTag($this);
        }

        return $this;
    }

    /**
     * Remove a song from the tag.
     *
     * @param Song $song param
     *
     * @return $this return
     */
    public function removeSong(Song $song): static
    {
        if ($this->songs->removeElement($song)) {
            $song->removeTag($this);
        }

        return $this;
    }
}
