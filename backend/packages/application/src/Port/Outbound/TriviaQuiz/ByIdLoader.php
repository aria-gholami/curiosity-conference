<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;

interface ByIdLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(Id $id): TriviaQuiz;
}
