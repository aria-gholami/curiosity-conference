<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Entity;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuizRoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
#[ORM\Entity(repositoryClass: TriviaQuizRoomRepository::class)]
final class TriviaQuizRoom
{
    public const string STATUS_CREATED = 'created';
    public const string STATUS_STARTED = 'started';
    public const string STATUS_FINISHED = 'finished';
    public const string STATUS_CLOSED = 'closed';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\ManyToOne(targetEntity: TriviaQuiz::class)]
    #[ORM\JoinColumn(nullable: false)]
    private TriviaQuiz $triviaQuiz;

    #[ORM\ManyToOne(targetEntity: TriviaQuestion::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?TriviaQuestion $currentTriviaQuestion;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $host;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'trivia_quiz_room_participants')]
    private Collection $triviaParticipants;

    /**
     * @var Collection<int, TriviaHighscore>
     */
    #[ORM\OneToMany(mappedBy: 'triviaQuizRoom', targetEntity: TriviaHighscore::class, orphanRemoval: true)]
    private Collection $triviaHighscores;

    #[ORM\Column(type: 'string', length: 255)]
    private string $status;

    public function __construct(string $id, string $title, TriviaQuiz $triviaQuiz, ?TriviaQuestion $currentTriviaQuestion, User $host, string $status)
    {
        $this->id = $id;
        $this->title = $title;
        $this->triviaQuiz = $triviaQuiz;
        $this->currentTriviaQuestion = $currentTriviaQuestion;
        $this->host = $host;
        $this->triviaParticipants = new ArrayCollection();
        $this->triviaHighscores = new ArrayCollection();
        $this->status = $status;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTriviaQuiz(): TriviaQuiz
    {
        return $this->triviaQuiz;
    }

    public function getCurrentTriviaQuestion(): ?TriviaQuestion
    {
        return $this->currentTriviaQuestion;
    }

    public function setCurrentTriviaQuestion(?TriviaQuestion $triviaQuestion): void
    {
        $this->currentTriviaQuestion = $triviaQuestion;
    }

    public function getHost(): User
    {
        return $this->host;
    }

    public function getTriviaParticipants(): Collection
    {
        return $this->triviaParticipants;
    }

    public function addTriviaParticipant(User $user): void
    {
        if ($this->triviaParticipants->contains($user)) {
            return;
        }

        $this->triviaParticipants->add($user);
    }

    public function removeTriviaParticipant(User $user): void
    {
        if (!$this->triviaParticipants->contains($user)) {
            return;
        }

        $this->triviaParticipants->removeElement($user);
    }

    public function getTriviaHighscores(): Collection
    {
        return $this->triviaHighscores;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
