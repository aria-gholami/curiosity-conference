<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Entity;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
#[ORM\Entity(repositoryClass: QuizRepository::class)]
final class Quiz
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(length: 255, unique: true)]
    private string $title;

    public function __construct(string $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
