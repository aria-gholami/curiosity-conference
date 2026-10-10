<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Application\Port\Inbound\TriviaQuiz\ActiveRoomLoadHandler as ActiveRoomLoadHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomByUserIdLoader;
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
    public function handleAndGet(Id $userId): ?TriviaQuizRoom
    {
        return $this->roomByUserIdLoader->load($userId->value);
    }
}
