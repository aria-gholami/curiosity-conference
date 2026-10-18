<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Entity;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuestionnaireAnswerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
#[ORM\Entity(repositoryClass: QuestionnaireAnswerRepository::class)]
final class QuestionnaireAnswer
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\ManyToOne(targetEntity: QuestionnaireQuestion::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private QuestionnaireQuestion $questionnaireQuestion;

    public function __construct(string $id, string $title, QuestionnaireQuestion $questionnaireQuestion)
    {
        $this->id = $id;
        $this->title = $title;
        $this->questionnaireQuestion = $questionnaireQuestion;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getQuestionnaireQuestion(): QuestionnaireQuestion
    {
        return $this->questionnaireQuestion;
    }
}
