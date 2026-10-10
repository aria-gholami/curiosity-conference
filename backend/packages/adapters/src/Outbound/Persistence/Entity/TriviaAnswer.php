<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Entity;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaAnswerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
#[ORM\Entity(repositoryClass: TriviaAnswerRepository::class)]
final class TriviaAnswer
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\ManyToOne(targetEntity: TriviaQuestion::class, inversedBy: 'triviaAnswerCollection')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?TriviaQuestion $triviaQuestion = null;

    #[ORM\Column]
    private bool $isCorrect;

    public function __construct(string $id, string $title, bool $isCorrect)
    {
        $this->id = $id;
        $this->title = $title;
        $this->isCorrect = $isCorrect;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setTriviaQuestion(?TriviaQuestion $triviaQuestion): void
    {
        $this->triviaQuestion = $triviaQuestion;
    }

    public function getTriviaQuestion(): ?TriviaQuestion
    {
        return $this->triviaQuestion;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isCorrect(): bool
    {
        return $this->isCorrect;
    }
}
