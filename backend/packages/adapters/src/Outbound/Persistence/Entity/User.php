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
    private string $userName;

    /**
     * @var Collection<int, Highscore>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Highscore::class, orphanRemoval: true)]
    private Collection $highscores;

    public function __construct(string $id, string $userName)
    {
        $this->id = $id;
        $this->userName = $userName;
        $this->highscores = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserName(): string
    {
        return $this->userName;
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
