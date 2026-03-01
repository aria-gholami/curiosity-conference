<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Entity;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuestionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
#[ORM\Entity(repositoryClass: QuestionRepository::class)]
final class Question
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\ManyToOne(targetEntity: Quiz::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Quiz $quiz;

    /**
     * @var Collection<int, Answer>
     */
    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Answer::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $answerCollection;

    public function __construct(string $id, string $title, Quiz $quiz, Collection $answerCollection)
    {
        $this->id = $id;
        $this->quiz = $quiz;
        $this->answerCollection = $answerCollection;
        $this->title = $title;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    public function addAnswer(Answer $answer): void
    {
        if (!$this->answerCollection->contains($answer)) {
            $this->answerCollection->add($answer);
            $answer->setQuestion($this);
        }
    }

    public function getAnswerCollection(): Collection
    {
        return $this->answerCollection;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
