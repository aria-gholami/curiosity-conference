<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomParticipantPersister as RoomParticipantPersisterInterface;
use CuriosityConference\Domain\Exception\PersistFailedException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class RoomParticipantPersister implements RoomParticipantPersisterInterface
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
    public function persist(QuizRoom $quizRoom): QuizRoom
    {
        try {
            $this->logger->info('Start persisting new quiz room participant.', [
                'quizId' => $quizRoom->getId(),
            ]);
            $this->quizRoomEntityManager->persist($quizRoom);
            $this->quizRoomEntityManager->flush();
            $this->logger->info('Persisting new quiz room participant was successful.', [
                'quizId' => $quizRoom->getId(),
            ]);

            return $quizRoom;
        } catch (\Throwable $exception) {
            $this->logger->notice('Persisting new quiz room participant was not successful.', [
                'quizId' => $quizRoom->getId(),
            ]);
            $this->logger->debug('Persisting new quiz room participant was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
                'quizId' => $quizRoom->getId(),
            ]);

            throw PersistFailedException::persistFailed(QuizRoom::class);
        }
    }
}
