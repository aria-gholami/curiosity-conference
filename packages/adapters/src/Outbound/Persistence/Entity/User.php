<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Entity;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
final class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(length: 255)]
    private string $firstName;

    #[ORM\Column(length: 255)]
    private string $lastName;

    #[ORM\Column(length: 255, unique: true)]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $password;

    #[ORM\Column(type: 'boolean')]
    private bool $isAdmin;

    /**
     * @var Collection<int, Highscore>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Highscore::class, orphanRemoval: true)]
    private Collection $highscores;

    public function __construct(string $id, string $firstName, string $lastName, string $email, string $password, bool $isAdmin)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->isAdmin = $isAdmin;
        $this->highscores = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function getHighscores(): Collection
    {
        return $this->highscores;
    }

    public function addHighscore(Highscore $highscore): void
    {
        $this->highscores->add($highscore);
    }
}
