<?php
/**
 * Song entity.
 */

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Song class representing a song entity in the application.
 */
#[ORM\Entity(repositoryClass: SongRepository::class)]
class Song
{
    // Unique identifier for the song.
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Title of the song.
    #[ORM\Column(length: 255)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 64)]
    private ?string $title = null;

    // Timestamp for when the song was created.
    #[ORM\Column]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    // Timestamp for when the song was last updated.
    #[ORM\Column]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Tag> collection of tags associated with this song
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'songs', fetch: 'EXTRA_LAZY')]
    private Collection $tags;

    // Category associated with the song.
    #[ORM\ManyToOne(Category::class, fetch: 'EXTRA_LAZY', inversedBy: 'songs')]
    private ?Category $category = null;

    // Duration of the song (stored as TIME_MUTABLE).
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $duration = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(mappedBy: 'song', targetEntity: Comment::class, cascade: ['remove'])]
    private Collection $comments;

    //    // Comment associated with the song.
    //    #[ORM\Column(type: Types::TEXT)]
    //    private ?string $comment = null;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * @return int|null return
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null return
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
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @param Tag $tag param
     *
     * @return $this return
     */
    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    /**
     * @param Tag $tag param
     *
     * @return $this return
     */
    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * @return Category|null return
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category|null $category param
     *
     * @return $this return
     */
    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null return
     */
    public function getDuration(): ?\DateTimeInterface
    {
        return $this->duration;
    }

    /**
     * @param \DateTimeInterface $duration param
     *
     * @return $this return
     */
    public function setDuration(\DateTimeInterface $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    //    /**
    //     * @return string|null return
    //     */
    //    public function getComment(): ?string
    //    {
    //        return $this->comment;
    //    }

    //    /**
    //     * @param string|null $comment param
    //     *
    //     * @return $this return
    //     */
    //    public function setComment(?string $comment): static
    //    {
    //        $this->comment = $comment;
    //
    //        return $this;
    //    }

    /**
     * Get the formatted updated date.
     *
     * @return string Formatted date
     */
    public function getFormattedUpdatedAt(): string
    {
        return $this->updatedAt->format('Y\m\d');
    }

    /**
     * Get the formatted created date.
     *
     * @return string Formatted date
     */
    public function getFormattedCreatedAt(): string
    {
        return $this->createdAt->format('Y\m\d');
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment param
     *
     * @return $this return
     */
    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setSong($this);
        }

        return $this;
    }

    /**
     * @param Comment $comment paramm
     *
     * @return $this retur
     */
    public function removeComment(Comment $comment): static
    {
        // set the owning side to null (unless already changed)
        if ($this->comments->removeElement($comment) && $comment->getSong() === $this) {
            $comment->setSong(null);
        }

        return $this;
    }
}
