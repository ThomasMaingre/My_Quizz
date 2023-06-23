<?php

namespace App\Entity;

use App\Repository\ActiviteRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: ActiviteRepository::class)]
/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="activite",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="unique_combination", columns={"user_id", "session_id", "categorie_id"})
 *     }
 * )
 */
class Activite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Categorie::class)]
    #[ORM\JoinColumn(name: 'categorie_id', referencedColumnName: 'id', nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\Column(type: 'integer')]
    private ?int $score = null;

    #[ORM\Column(name: "session_id", type: 'string')]
    private ?string $session = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }



    public function setScore(?int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getSession(): ?string
    {
        return $this->session;
    }

    public function setSession(?string $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score ?? 0;
    }

    public function getQuiz(): ?Categorie
    {
        return $this->categorie;
    }

    public function setQuiz(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }
}
