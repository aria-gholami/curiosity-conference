<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Application\Port\Inbound\Quiz\ListRoomHandler as ListRoomHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomLoader;
use CuriosityConference\Domain\Quiz\QuizRoomCollection;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class ListRoomHandler implements ListRoomHandlerInterface
{
    public function __construct(
        private RoomLoader $roomLoader,
    ) {
    }

    #[\Override]
    public function handleAndGet(): QuizRoomCollection
    {
        return $this->roomLoader->load();
    }
}
