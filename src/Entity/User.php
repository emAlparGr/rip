<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type:'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;
    
    #[ORM\Column(type: 'string', length: 255)]
    private string $password;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(type: 'string', unique: true, nullable: true)]
    private $apiToken;

    #[ORM\OneToMany(mappedBy:'user', targetEntity: Quote::class)]
    private $quotes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): string 
    {
        return $this->getName();
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getSalt(): string
    {
        return '__SALT__';
    }

    public function setApiToken(string $apiToken): void
    {
        $this->apiToken = $apiToken;
    }

    public function getApiToken(): string
    {
        return $this->apiToken;
    }

    public function eraseCredentials(): void
    {
        // $this->plainPassword = null;
    }

    public function getQuotes(): Collection
    {
        return $this->quotes;
    }
}
