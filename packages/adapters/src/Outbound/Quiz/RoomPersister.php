<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomPersister as RoomPersisterInterface;
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
    public function persist(Quiz $quiz, User $host, string $status): string
    {
        try {
            $this->logger->info('Start persisting new quiz room.', [
                'quizId' => $quiz->getId(),
            ]);
            $quizRoom = new QuizRoom(
                Uuid::v4()->toRfc4122(),
                $quiz,
                null,
                $host,
                $status,
            );
            $quizRoom->addParticipant($host);
            $this->quizRoomEntityManager->persist($quizRoom);
            $this->quizRoomEntityManager->flush();
            $this->logger->info('Persisting new quiz room was successful.', [
                'quizId' => $quiz->getId(),
            ]);

            return $quizRoom->getId();
        } catch (\Throwable $exception) {
            $this->logger->notice('Persisting new quiz room was not successful.', [
                'quizId' => $quiz->getId(),
            ]);
            $this->logger->debug('Persisting new quiz room was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
                'quizId' => $quiz->getId(),
            ]);

            throw PersistFailedException::persistFailed(QuizRoom::class);
        }
    }
}
