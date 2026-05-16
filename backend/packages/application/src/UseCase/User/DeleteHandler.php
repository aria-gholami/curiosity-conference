<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Application\Port\Inbound\User\DeleteHandler as DeleteHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByUserIdLoader;
use CuriosityConference\Application\Port\Outbound\User\ByIdDeleter;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Model\Id;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final readonly class DeleteHandler implements DeleteHandlerInterface
{
    public function __construct(
        private RoomByUserIdLoader $roomByUserIdLoader,
        private ByIdDeleter $byIdDeleter,
        private HubInterface $hub,
    ) {
    }

    /**
     * @throws DeleteFailedException
     * @throws LoadEntityFailedException
     * @throws \JsonException
     */
    #[\Override]
    public function handle(Id $userId): void
    {
        $quizRoom = $this->roomByUserIdLoader->load($userId->value);
        $this->byIdDeleter->delete($userId);
        if (!$quizRoom instanceof QuizRoom) {
            return;
        }

        $this->hub->publish(new Update(
            'quiz/room/list',
            \json_encode(['action' => 'remove', 'quizRoomId' => $quizRoom->getId()], \JSON_THROW_ON_ERROR),
        ));
    }
}
