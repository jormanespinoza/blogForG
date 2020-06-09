<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
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
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coverImage;

    /**
     * @Assert\File(
     *      mimeTypes = {"image/jpeg", "image/x-citrix-jpeg", "image/png", "image/x-png", "image/x-citrix-png"},
     *      mimeTypesMessage = "Por favor, sube un formato válido (jpg/.png)",
     *      maxSize = "2048k"
     * )
     * @Assert\Image(
     *      minWidth = 767
     * )
     * @Vich\UploadableField(mapping="post", fileNameProperty="coverImage", size="coverImageSize")
     * @var File
     */
    private $coverImageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mainImage;

    /**
     * @Assert\File(
     *      mimeTypes = {"image/jpeg", "image/x-citrix-jpeg", "image/png", "image/x-png", "image/x-citrix-png"},
     *      mimeTypesMessage = "Por favor, sube un formato válido (jpg/.png)",
     *      maxSize = "2048k"
     * )
     * @Assert\Image(
     *      minWidth = 767
     * )
     * @Vich\UploadableField(mapping="post", fileNameProperty="mainImage", size="mainImageSize")
     * @var File
     */
    private $mainImageFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $rejected;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     */
    private $user;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $likes;

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

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $coverImage
     */
    public function setCoverImageFile(?File $coverImage = null): void
    {
        $this->coverImageFile = $coverImage;

        if (null !== $coverImage) {
            $this->updateDate();
        }
    }

    public function getCoverImageFile(): ?File
    {
        return $this->coverImageFile;
    }

    public function setCoverImage(?string $coverImage): void
    {
        $this->coverImage = $coverImage;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImageSize(?int $coverImageSize): void
    {
        $this->coverImageSize = $coverImageSize;
    }

    public function getCoverImageSize(): ?int
    {
        return $this->coverImageSize;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $mainImage
     */
    public function setMainImageFile(?File $mainImage = null): void
    {
        $this->mainImageFile = $mainImage;

        if (null !== $mainImage) {
            $this->updateDate();
        }
    }

    public function getMainImageFile(): ?File
    {
        return $this->mainImageFile;
    }

    public function setMainImage(?string $mainImage): void
    {
        $this->mainImage = $mainImage;
    }

    public function getMainImage(): ?string
    {
        return $this->mainImage;
    }

    public function setMainImageSize(?int $mainImageSize): void
    {
        $this->mainImageSize = $mainImageSize;
    }

    public function getMainImageSize(): ?int
    {
        return $this->mainImageSize;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getRejected(): ?bool
    {
        return $this->rejected;
    }

    public function setRejected(?bool $rejected): self
    {
        $this->rejected = $rejected;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Este método actualiza la fecha de edición del post
     * esto es necesario en caso de actualizar <solo> la imagen principal
     * de esta forma el listener de cambio de imagen se dispara actualizando la misma
     */
    public function updateDate()
    {
        // It is required that at least one field changes if you are using doctrine
        // otherwise the event listeners won't be called and the file is lost
        $this->updatedAt = new \DateTimeImmutable();
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

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }
}