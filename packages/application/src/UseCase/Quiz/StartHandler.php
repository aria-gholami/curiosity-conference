<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Application\Port\Inbound\Quiz\StartHandler as StartHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Question\ByIdLoader;
use CuriosityConference\Application\Port\Outbound\Quiz\QuizRoomUpdater;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByIdLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Id;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class StartHandler implements StartHandlerInterface
{
    public function __construct(
        private QuizRoomUpdater $quizRoomUpdater,
        private RoomByIdLoader $roomByIdLoader,
        private ByIdLoader $byIdLoader,
    ) {
    }

    /**
     * @throws UpdateFailedException
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function Handle(Id $quizRoomId): void
    {
        $quizRoom = $this->roomByIdLoader->load($quizRoomId->value);
        $questionCollection = $this->byIdLoader->load($quizRoom->getQuiz());
        $this->quizRoomUpdater->update($quizRoomId, $questionCollection->data[0], QuizRoom::STATUS_STARTED);
    }
}
