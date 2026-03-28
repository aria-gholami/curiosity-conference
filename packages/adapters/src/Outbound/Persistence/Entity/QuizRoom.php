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
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Quiz::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Quiz $quiz;

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

    #[ORM\Column(type: 'boolean')]
    private bool $isStarted;

    public function __construct(string $id, Quiz $quiz, User $host, bool $isStarted)
    {
        $this->id = $id;
        $this->quiz = $quiz;
        $this->host = $host;
        $this->participants = new ArrayCollection();
        $this->highscores = new ArrayCollection();
        $this->isStarted = $isStarted;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getQuiz(): Quiz
    {
        return $this->quiz;
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

    public function getHighscores(): Collection
    {
        return $this->highscores;
    }

    public function setIsStarted(bool $isStarted): void
    {
        $this->isStarted = $isStarted;
    }

    public function isStarted(): bool
    {
        return $this->isStarted;
    }
}
