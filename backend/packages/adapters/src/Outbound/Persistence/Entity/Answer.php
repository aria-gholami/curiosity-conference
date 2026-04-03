<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Entity;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\AnswerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
#[ORM\Entity(repositoryClass: AnswerRepository::class)]
final class Answer
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\ManyToOne(targetEntity: Question::class, inversedBy: 'answerCollection')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Question $question = null;

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

    public function setQuestion(?Question $question): void
    {
        $this->question = $question;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
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
