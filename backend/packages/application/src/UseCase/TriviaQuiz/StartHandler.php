<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Application\Port\Inbound\TriviaQuiz\StartHandler as StartHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuestion\ByIdLoader;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\HighscorePersister;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomByIdLoader;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomUpdater;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final readonly class StartHandler implements StartHandlerInterface
{
    public function __construct(
        private RoomUpdater $roomUpdater,
        private RoomByIdLoader $roomByIdLoader,
        private ByIdLoader $byIdLoader,
        private HighscorePersister $highscorePersister,
        private HubInterface $hub,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     * @throws UpdateFailedException
     * @throws \JsonException
     */
    #[\Override]
    public function handle(Id $quizRoomId): void
    {
        $quizRoom = $this->roomByIdLoader->load($quizRoomId->value);
        $questionCollection = $this->byIdLoader->load($quizRoom->getTriviaQuiz());
        $quizRoom->setCurrentTriviaQuestion($questionCollection->data[0]);
        $quizRoom->setStatus(TriviaQuizRoom::STATUS_STARTED);
        $this->roomUpdater->update($quizRoom);

        $participantCollection = $quizRoom->getTriviaParticipants();
        foreach ($participantCollection as $participant) {
            $this->highscorePersister->persist($quizRoom, $participant);
        }

        $this->hub->publish(new Update(
            \sprintf('trivia/quiz/room/%s/status', $quizRoomId->value),
            \json_encode(['action' => 'started'], \JSON_THROW_ON_ERROR),
        ));
        $this->hub->publish(new Update(
            'trivia/quiz/room/list',
            \json_encode(['action' => 'status', 'status' => 'started', 'quizRoomId' => $quizRoomId->value], \JSON_THROW_ON_ERROR),
        ));
    }
}
