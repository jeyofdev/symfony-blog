<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @Vich\Uploadable
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 3,
     *      max = 255,
     *      minMessage = "The title must be at least {{ limit }} characters long",
     *      maxMessage = "The title cannot be longer than {{ limit }} characters"
     * )
     */
    private $title;


    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 20,
     *      minMessage = "The content must be at least {{ limit }} characters long"
     * )
     */
    private $content;


    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;


    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;


    /**
     * @ORM\Column(type="smallint")
     */
    private $published;


    /**
        * @var File|null
        * @Vich\UploadableField(mapping="property_image", fileNameProperty="filename")
        * @Assert\Image(
        *      mimeTypes = "image/jpeg"
        * )
        */
    private $imageFile;


    /**
    * @var string|null
    * @ORM\Column(type="string", nullable=true, length=255)
    */
    private $filename;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="posts")
     */
    private $categories;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="post", orphanRemoval=true)
     */
    private $comments;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getTitle(): ?string
    {
        return $this->title;
    }


    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }


    public function getContent(): ?string
    {
        return $this->content;
    }


    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }


    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }


    public function getupdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }


    public function setupdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }


    public function getSlug(): ?string
    {
        return $this->slug;
    }


    public function setSlug(): self
    {
        $this->slug = (new Slugify())->slugify($this->title);

        return $this;
    }


    public function getPublished(): ?int
    {
        return $this->published;
    }


    public function setPublished(int $published): self
    {
        $this->published = $published;

        return $this;
    }


    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }


    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $imageFile
     */
    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if (!is_null($imageFile)) {
            $this->updated_at = new DateTime();
        }

        return $this;
    }


    public function getFilename(): ?string
    {
        return $this->filename;
    }


    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }


    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }


    public function setCategories(?Category $category): self
    {
        $this->categories[] = $category;
        return $this;
    }


    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addPost($this);
        }

        return $this;
    }


    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            $category->removePost($this);
        }

        return $this;
    }


    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }


    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }


    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }


    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
