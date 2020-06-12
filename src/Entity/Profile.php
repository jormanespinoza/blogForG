<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProfileRepository::class)
 * @Vich\Uploadable
 */
class Profile implements \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profileImage;

    /**
     * @Assert\File(
     *      mimeTypes = {"image/jpeg", "image/x-citrix-jpeg", "image/png", "image/x-png", "image/x-citrix-png"},
     *      mimeTypesMessage = "Por favor, sube un formato válido (jpg/.png)",
     *      maxSize = "2048k"
     * )
     * @Assert\Image(
     *      minWidth = 250
     * )
     * @Vich\UploadableField(mapping="user", fileNameProperty="profileImage", size="profileImageSize")
     * @var File
     */
    private $profileImageFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="profile", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $profileImage
     */
    public function setProfileImageFile(?File $profileImage = null): void
    {
        $this->profileImageFile = $profileImage;

        if (null !== $profileImage) {
            $this->updateDate();
        }
    }

    public function getProfileImageFile(): ?File
    {
        return $this->profileImageFile;
    }

    public function setProfileImage(?string $profileImage): void
    {
        $this->profileImage = $profileImage;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImageSize(?int $profileImageSize): void
    {
        $this->profileImageSize = $profileImageSize;
    }

    public function getProfileImageSize(): ?int
    {
        return $this->profileImageSize;
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

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function serialize()
    {
        $this->profileImage = base64_encode($this->profileImage);
    }

    public function unserialize($serialized)
    {
        $this->profileImage = base64_decode($this->profileImage);

    }
}
