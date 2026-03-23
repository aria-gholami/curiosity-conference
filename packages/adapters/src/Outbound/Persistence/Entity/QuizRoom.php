<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Entity;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRoomRepository;
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

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'quiz_room_participants')]
    private Collection $participants;

    public function __construct(string $id, Quiz $quiz, User $host, Collection $participants)
    {
        $this->id = $id;
        $this->quiz = $quiz;
        $this->host = $host;
        $this->participants = $participants;
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
}
