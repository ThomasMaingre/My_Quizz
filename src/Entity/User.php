<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\UserRepository;
use Symfony\Component\Uid\Uuid;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "users")]
class User implements UserInterface, PasswordAuthenticatedUserInterface

{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $emailVerifiedAt = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $confirmation_token = null;

    #[ORM\Column(type: 'boolean')]
    private bool $confirmed = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    #[ORM\Column(type: 'datetime', nullable:true)]
    private ?\DateTimeInterface $LastConnection = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getPassword(): ?string
    {
        // Implement your logic to retrieve the user's hashed password from the entity
        return $this->password;
    }

    public function getSalt(): ?string
    {
        // Implement any additional salt logic if necessary
        return null;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmailVerifiedAt(): ?\DateTimeInterface
    {
        return $this->emailVerifiedAt;
    }

    public function setEmailVerifiedAt(?\DateTimeInterface $emailVerifiedAt): self
    {
        $this->emailVerifiedAt = $emailVerifiedAt;

        return $this;
    }

    public function getVerificationToken(): ?string
    {
        return $this->confirmation_token = Uuid::v4();
    }

    public function getConfirmation_Token(): ?string
    {
        return $this->confirmation_token;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmation_token = $confirmationToken;

        return $this;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    public function getLastConnection(): ?\DateTimeInterface
    {
        return $this->LastConnection;
    }
    
    public function setLastConnection(?\DateTimeInterface $LastConnection): self
    {
        $this->LastConnection = $LastConnection;
    
        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    public function getRoles(): array
    {
        // Implement the logic to return the user's roles
        return [];
    }

    public function eraseCredentials()
    {
        // Implement any logic to erase sensitive data from the user object if needed
    }
    public function getUserIdentifier(): string
    {
        // Implement the logic to return the user's identifier (e.g., email, username)
        return $this->email;
    }

    public function getUsername(): ?string
    {
        return $this->name;
    }
}
