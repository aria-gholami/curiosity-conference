<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Application\Port\Inbound\TriviaQuiz\CloseRoomHandler as CloseRoomHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomByIdLoader;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomUpdater;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;
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
        $quizRoom->setStatus(TriviaQuizRoom::STATUS_CLOSED);
        $this->roomUpdater->update($quizRoom);
        $this->hub->publish(new Update(
            'trivia/quiz/room/list',
            \json_encode(['action' => 'status', 'status' => TriviaQuizRoom::STATUS_CLOSED, 'quizRoomId' => $quizRoomId->value], \JSON_THROW_ON_ERROR),
        ));
    }
}
