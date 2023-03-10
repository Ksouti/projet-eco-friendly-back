<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"articles"})
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(mode = "strict", message="L’email est invalide.")
     * @Assert\NotBlank
     * @Groups({"articles"})
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $email;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Regex(pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).{8,32}$/", groups={"registration"}, message="Le mot de passe doit contenir au moins 8 caractères dont une minuscule, une majuscule, un chiffre et un caractère spécial")
     * @Assert\NotBlank(groups={"registration"}) 
     */
    private $password;

    /**
     * @ORM\Column(type="json")
     * @Assert\NotBlank
     * @Groups({"articles"})
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(max = 64)
     * starts with a capital letter & contains only letters, hyphens and apostrophes
     * @Assert\Regex(pattern="/^[A-Z][A-Za-zàâçéèêëîïôûùüÿñæœ\s\-\']*$/")
     * @Groups({"articles"})
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(max = 64)
     * starts with a capital letter & contains only letters, hyphens and apostrophes
     * @Assert\Regex(pattern="/^[A-Z][A-Za-zàâçéèêëîïôûùüÿñæœ\s\-\']*$/")
     * @Groups({"articles"})
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\Length(min = 3, max = 64)
     * @Assert\Regex(pattern="/^[^\#\s]+$/", message="Votre pseudo ne peut pas contenir d'espace ou de #")
     * @Assert\NotBlank
     * @Groups({"articles"})
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=5, unique=true)
     * @Assert\Regex(pattern="/^[\#][\d]{4}$/")
     * @Assert\NotBlank
     * @Groups({"articles"})
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max = 255)
     * @Assert\Url
     * @Assert\NotBlank
     * @Groups({"articles"})
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $avatar;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\Type("bool")
     * @Groups({"articles"})
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $is_active;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\Type("bool")
     * @Groups({"articles"})
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $is_verified;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\NotBlank
     * @Assert\Type("DateTimeImmutable")
     * @Groups({"articles"})
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Assert\Type("DateTimeImmutable")
     * @Groups({"articles"})
     * @Groups({"advices"})
     * @Groups({"users"})
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="author")
     */
    private $articles;

    /**
     * @ORM\OneToMany(targetEntity=Advice::class, mappedBy="contributor")
     * @Groups({"users"})
     */
    private $advices;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->advices = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setAuthor($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getAuthor() === $this) {
                $article->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Advice>
     */
    public function getAdvices(): Collection
    {
        return $this->advices;
    }

    public function addAdvice(Advice $advice): self
    {
        if (!$this->advices->contains($advice)) {
            $this->advices[] = $advice;
            $advice->setContributor($this);
        }

        return $this;
    }

    public function removeAdvice(Advice $advice): self
    {
        if ($this->advices->removeElement($advice)) {
            // set the owning side to null (unless already changed)
            if ($advice->getContributor() === $this) {
                $advice->setContributor(null);
            }
        }

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

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

    public function isVerified(): ?bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(bool $is_verified): self
    {
        $this->is_verified = $is_verified;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function isIsVerified(): ?bool
    {
        return $this->is_verified;
    }
}
