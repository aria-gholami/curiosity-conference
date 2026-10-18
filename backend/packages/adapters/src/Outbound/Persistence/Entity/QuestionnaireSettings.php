<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Entity;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuestionnaireQuestionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
#[ORM\Entity(repositoryClass: QuestionnaireQuestionRepository::class)]
final class QuestionnaireSettings
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(type: 'boolean')]
    private bool $showResults;

    public function __construct(string $id, bool $showResults)
    {
        $this->id = $id;
        $this->showResults = $showResults;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setIsShownResults(bool $showResults): void
    {
        $this->showResults = $showResults;
    }

    public function isShownResults(): bool
    {
        return $this->showResults;
    }
}
