<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Entity;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuestionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
#[ORM\Entity(repositoryClass: TriviaQuestionRepository::class)]
final class TriviaQuestion
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(length: 255)]
    private string $title;

    /**
     * @var Collection<int, TriviaAnswer>
     */
    #[ORM\OneToMany(mappedBy: 'triviaQuestion', targetEntity: TriviaAnswer::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $triviaAnswerCollection;

    public function __construct(string $id, string $title, Collection $triviaAnswerCollection)
    {
        $this->id = $id;
        $this->triviaAnswerCollection = $triviaAnswerCollection;
        $this->title = $title;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function addTriviaAnswer(TriviaAnswer $triviaAnswer): void
    {
        if (!$this->triviaAnswerCollection->contains($triviaAnswer)) {
            $this->triviaAnswerCollection->add($triviaAnswer);
            $triviaAnswer->setTriviaQuestion($this);
        }
    }

    public function getTriviaAnswerCollection(): Collection
    {
        return $this->triviaAnswerCollection;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
