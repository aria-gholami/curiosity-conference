<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Application\Port\Inbound\Quiz\ActiveRoomLoadHandler as ActiveRoomLoadHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByUserIdLoader;
use CuriosityConference\Domain\User\Model\Id;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class ActiveRoomLoadHandler implements ActiveRoomLoadHandlerInterface
{
    public function __construct(
        private RoomByUserIdLoader $roomByUserIdLoader,
    ) {
    }

    #[\Override]
    public function handleAndGet(Id $userId): ?QuizRoom
    {
        return $this->roomByUserIdLoader->load($userId->value);
    }
}
