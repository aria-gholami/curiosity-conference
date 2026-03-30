<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Entity;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\HighscoreRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
#[ORM\Entity(repositoryClass: HighscoreRepository::class)]
final class Highscore
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: QuizRoom::class, inversedBy: 'highscores')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private QuizRoom $quizRoom;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'highscores')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column]
    private int $score;

    public function __construct(string $id, QuizRoom $quizRoom, User $user, int $score)
    {
        $this->id = $id;
        $this->quizRoom = $quizRoom;
        $this->user = $user;
        $this->score = $score;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getQuizRoom(): QuizRoom
    {
        return $this->quizRoom;
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
