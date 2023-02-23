<?php

namespace App\Entity;

use App\Repository\AdviceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AdviceRepository::class)
 */
class Advice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\Length(min = 1, max = 128)
     * @Assert\NotBlank
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\Length(min = 1, max = 128)
     * @Assert\NotBlank
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $slug;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Length(min = 1, max = 128)
     * @Assert\NotBlank
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $status;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\NotBlank
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="advices")
     * @ORM\JoinColumn(name="contributor_id", referencedColumnName="id")
     * @Assert\NotBlank
     * @Groups({"advices"})
     */

    private $contributor;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="advices")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $category;


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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getContributor(): ?User
    {
        return $this->contributor;
    }

    public function setContributor(?User $contributor): self
    {
        $this->contributor = $contributor;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
