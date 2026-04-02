<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Application\Port\Inbound\Quiz\StartHandler as StartHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Question\ByIdLoader;
use CuriosityConference\Application\Port\Outbound\Quiz\HighscorePersister;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByIdLoader;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomUpdater;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\Id;

final readonly class StartHandler implements StartHandlerInterface
{
    public function __construct(
        private RoomUpdater $roomUpdater,
        private RoomByIdLoader $roomByIdLoader,
        private ByIdLoader $byIdLoader,
        private HighscorePersister $highscorePersister,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     */
    #[\Override]
    public function handle(Id $quizRoomId): void
    {
        $quizRoom = $this->roomByIdLoader->load($quizRoomId->value);
        $questionCollection = $this->byIdLoader->load($quizRoom->getQuiz());
        $quizRoom->setCurrentQuestion($questionCollection->data[0]);
        $quizRoom->setStatus(QuizRoom::STATUS_STARTED);
        $this->roomUpdater->update($quizRoom);

        $participantCollection = $quizRoom->getParticipants();
        foreach ($participantCollection as $participant) {
            $this->highscorePersister->persist($quizRoom, $participant);
        }
    }
}
