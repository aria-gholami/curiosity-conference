<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomPersister as RoomPersisterInterface;
use CuriosityConference\Domain\Exception\PersistFailedException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class RoomPersister implements RoomPersisterInterface
{
    public function __construct(
        private EntityManagerInterface $quizRoomEntityManager,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws PersistFailedException
     */
    #[\Override]
    public function persist(User $host, string $title, string $status): TriviaQuizRoom
    {
        try {
            $this->logger->info('Start persisting new quiz room.', [
                'quizRoomTitle' => $title,
            ]);
            $quizRoom = new TriviaQuizRoom(
                Uuid::v4()->toRfc4122(),
                $title,
                null,
                $host,
                $status,
            );
            $quizRoom->addTriviaParticipant($host);
            $this->quizRoomEntityManager->persist($quizRoom);
            $this->quizRoomEntityManager->flush();
            $this->logger->info('Persisting new quiz room was successful.', [
                'quizRoomTitle' => $title,
            ]);

            return $quizRoom;
        } catch (\Throwable $exception) {
            $this->logger->notice('Persisting new quiz room was not successful.', [
                'quizRoomTitle' => $title,
            ]);
            $this->logger->debug('Persisting new quiz room was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
                'quizRoomTitle' => $title,
            ]);

            throw PersistFailedException::persistFailed(TriviaQuizRoom::class);
        }
    }
}
