<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Entity;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuestionnaireSubmissionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
#[ORM\Entity(repositoryClass: QuestionnaireSubmissionRepository::class)]
final class QuestionnaireSubmission
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'questionnaireSubmissions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    /**
     * @var Collection<int, QuestionnaireAnswer>
     */
    #[ORM\OneToMany(mappedBy: 'questionnaire_answer', targetEntity: QuestionnaireAnswer::class)]
    private Collection $questionnaireAnswerCollection;

    public function __construct(string $id, User $user, Collection $questionnaireAnswerCollection)
    {
        $this->id = $id;
        $this->user = $user;
        $this->questionnaireAnswerCollection = $questionnaireAnswerCollection;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getQuestionnaireAnswerCollection(): Collection
    {
        return $this->questionnaireAnswerCollection;
    }
}
