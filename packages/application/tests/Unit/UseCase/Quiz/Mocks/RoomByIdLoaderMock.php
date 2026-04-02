<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByIdLoader;

final class RoomByIdLoaderMock implements RoomByIdLoader
{
    public string $inputId;
    public QuizRoom $outputQuizRoom;

    #[\Override]
    public function load(string $id): QuizRoom
    {
        $this->inputId = $id;

        return $this->outputQuizRoom;
    }
}
