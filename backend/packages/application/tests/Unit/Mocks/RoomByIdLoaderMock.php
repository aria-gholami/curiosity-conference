<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomByIdLoader;

final class RoomByIdLoaderMock implements RoomByIdLoader
{
    public string $inputId;
    public TriviaQuizRoom $outputQuizRoom;

    #[\Override]
    public function load(string $id): TriviaQuizRoom
    {
        $this->inputId = $id;

        return $this->outputQuizRoom;
    }
}
