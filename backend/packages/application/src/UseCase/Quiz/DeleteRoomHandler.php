<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Application\Port\Inbound\Quiz\DeleteRoomHandler as DeleteRoomHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByIdDeleter;
use CuriosityConference\Domain\Quiz\Id;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class DeleteRoomHandler implements DeleteRoomHandlerInterface
{
    public function __construct(
        private RoomByIdDeleter $roomByIdDeleter,
        private HubInterface $hub,
    ) {
    }

    #[\Override]
    public function handle(Id $quizRoomId): void
    {
        $this->roomByIdDeleter->delete($quizRoomId);
        $this->hub->publish(new Update(
            'quiz/room/list',
            \json_encode(['action' => 'remove', 'quizRoomId' => $quizRoomId->value], \JSON_THROW_ON_ERROR),
        ));
        $this->hub->publish(new Update(
            \sprintf('quiz/room/%s/participants', $quizRoomId->value),
            \json_encode(['action' => 'removeQuizRoom', 'quizRoomId' => $quizRoomId->value], \JSON_THROW_ON_ERROR),
        ));
    }
}
