<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Application\Port\Inbound\Quiz\CloseRoomHandler as CloseRoomHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByIdLoader;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomUpdater;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Id;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class CloseRoomHandler implements CloseRoomHandlerInterface
{
    public function __construct(
        private RoomByIdLoader $roomByIdLoader,
        private RoomUpdater $roomUpdater,
        private HubInterface $hub,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     * @throws UpdateFailedException
     * @throws \JsonException
     */
    #[\Override]
    public function handle(Id $quizRoomId): void
    {
        $quizRoom = $this->roomByIdLoader->load($quizRoomId->value);
        $quizRoom->setStatus(QuizRoom::STATUS_CLOSED);
        $this->roomUpdater->update($quizRoom);
        $this->hub->publish(new Update(
            'quiz/room/list',
            \json_encode(['action' => 'status', 'status' => QuizRoom::STATUS_CLOSED, 'quizRoomId' => $quizRoomId->value], \JSON_THROW_ON_ERROR),
        ));
    }
}
