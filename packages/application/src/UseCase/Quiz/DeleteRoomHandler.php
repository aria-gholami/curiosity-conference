<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Application\Port\Inbound\Quiz\DeleteRoomHandler as DeleteRoomHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByIdDeleter;
use CuriosityConference\Domain\Quiz\Id;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class DeleteRoomHandler implements DeleteRoomHandlerInterface
{
    public function __construct(
        private RoomByIdDeleter $roomByIdDeleter,
    ) {
    }

    #[\Override]
    public function handle(Id $quizRoomId): void
    {
        $this->roomByIdDeleter->delete($quizRoomId);
    }
}
