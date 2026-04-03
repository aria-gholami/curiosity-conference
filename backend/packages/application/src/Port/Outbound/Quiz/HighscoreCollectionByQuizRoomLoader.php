<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\HighscoreCollection;

interface HighscoreCollectionByQuizRoomLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(QuizRoom $quizRoom): HighscoreCollection;
}
