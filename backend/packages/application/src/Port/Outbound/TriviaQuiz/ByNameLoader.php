<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;

interface ByNameLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(string $title): ?TriviaQuiz;
}
