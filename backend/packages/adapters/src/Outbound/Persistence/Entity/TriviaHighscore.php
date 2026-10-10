<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Entity;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaHighscoreRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
#[ORM\Entity(repositoryClass: TriviaHighscoreRepository::class)]
final class TriviaHighscore
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: TriviaQuizRoom::class, inversedBy: 'triviaHighscores')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private TriviaQuizRoom $triviaQuizRoom;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'triviaHighscores')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column]
    private int $score;

    public function __construct(string $id, TriviaQuizRoom $triviaQuizRoom, User $user, int $score)
    {
        $this->id = $id;
        $this->triviaQuizRoom = $triviaQuizRoom;
        $this->user = $user;
        $this->score = $score;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTriviaQuizRoom(): TriviaQuizRoom
    {
        return $this->triviaQuizRoom;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function addScore(int $score): void
    {
        $this->score += $score;
    }
}
