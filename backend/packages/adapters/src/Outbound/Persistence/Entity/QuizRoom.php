<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Entity;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
#[ORM\Entity(repositoryClass: QuizRoomRepository::class)]
final class QuizRoom
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

    #[ORM\ManyToOne(targetEntity: Quiz::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Quiz $quiz;

    #[ORM\ManyToOne(targetEntity: Question::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Question $currentQuestion;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $host;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'quiz_room_participants')]
    private Collection $participants;

    /**
     * @var Collection<int, Highscore>
     */
    #[ORM\OneToMany(mappedBy: 'quizRoom', targetEntity: Highscore::class, orphanRemoval: true)]
    private Collection $highscores;

    #[ORM\Column(type: 'string', length: 255)]
    private string $status;

    public function __construct(string $id, string $title, Quiz $quiz, ?Question $currentQuestion, User $host, string $status)
    {
        $this->id = $id;
        $this->title = $title;
        $this->quiz = $quiz;
        $this->currentQuestion = $currentQuestion;
        $this->host = $host;
        $this->participants = new ArrayCollection();
        $this->highscores = new ArrayCollection();
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

    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    public function getCurrentQuestion(): ?Question
    {
        return $this->currentQuestion;
    }

    public function setCurrentQuestion(?Question $question): void
    {
        $this->currentQuestion = $question;
    }

    public function getHost(): User
    {
        return $this->host;
    }

    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $user): void
    {
        if ($this->participants->contains($user)) {
            return;
        }

        $this->participants->add($user);
    }

    public function removeParticipant(User $user): void
    {
        if (!$this->participants->contains($user)) {
            return;
        }

        $this->participants->removeElement($user);
    }

    public function getHighscores(): Collection
    {
        return $this->highscores;
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
