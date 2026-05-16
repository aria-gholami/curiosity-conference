<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByUserIdLoader;

final class RoomByUserIdLoaderMock implements RoomByUserIdLoader
{
    public string $inputUserId;
    public ?QuizRoom $outputQuizRoom;

    public function load(string $userId): ?QuizRoom
    {
        $this->inputUserId = $userId;

        return $this->outputQuizRoom;
    }
}
