<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email",message="This email is used")
 * @UniqueEntity(fields="username",message="This username is used")
 */
class User implements UserInterface, \Serializable
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min="5",max="100")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="4096")
     */
    private $plain_password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MicroPost", mappedBy="user")
     */
    private $microPosts;

    /**
     * @ORM\Column(type="simple_array")
     */
    private $roles = [];

    public function __construct()
    {
        $this->microPosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        return null;
    }

    public function serialize()
    {
        return \serialize(
            [$this->id, $this->username, $this->password,]
        );
    }

    public function unserialize($serialized)
    {
        [
            $this->id, $this->username, $this->password
        ] = \unserialize($serialized, [User::class]);
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plain_password;
    }

    /**
     * @param mixed $plain_password
     */
    public function setPlainPassword($plain_password): void
    {
        $this->plain_password = $plain_password;
    }

    /**
     * @return Collection|MicroPost[]
     */
    public function getMicroPosts(): Collection
    {
        return $this->microPosts;
    }

    public function addMicroPost(MicroPost $microPost): self
    {
        if (!$this->microPosts->contains($microPost)) {
            $this->microPosts[] = $microPost;
            $microPost->setUser($this);
        }

        return $this;
    }

    public function removeMicroPost(MicroPost $microPost): self
    {
        if ($this->microPosts->contains($microPost)) {
            $this->microPosts->removeElement($microPost);
            // set the owning side to null (unless already changed)
            if ($microPost->getUser() === $this) {
                $microPost->setUser(null);
            }
        }

        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
