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
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Id;
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
        $questionCollection = $this->byIdLoader->load($quizRoom->getQuiz());
        $quizRoom->setCurrentQuestion($questionCollection->data[0]);
        $quizRoom->setStatus(QuizRoom::STATUS_STARTED);
        $this->roomUpdater->update($quizRoom);

        $participantCollection = $quizRoom->getParticipants();
        foreach ($participantCollection as $participant) {
            $this->highscorePersister->persist($quizRoom, $participant);
        }

        $this->hub->publish(new Update(
            \sprintf('quiz/room/%s/status', $quizRoomId->value),
            \json_encode(['action' => 'started'], \JSON_THROW_ON_ERROR),
        ));
        $this->hub->publish(new Update(
            'quiz/room/list',
            \json_encode(['action' => 'status', 'status' => 'started', 'quizRoomId' => $quizRoomId->value], \JSON_THROW_ON_ERROR),
        ));
    }
}
