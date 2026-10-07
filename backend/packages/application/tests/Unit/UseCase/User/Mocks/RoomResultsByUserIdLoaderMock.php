<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks;

use CuriosityConference\Application\Port\Outbound\Quiz\RoomResultsByUserIdLoader;

final class RoomResultsByUserIdLoaderMock implements RoomResultsByUserIdLoader
{
    public string $inputUserId;
    public array $outputQuizRoomResults;

    public function load(string $userId): array
    {
        $this->inputUserId = $userId;

        return $this->outputQuizRoomResults;
    }
}
