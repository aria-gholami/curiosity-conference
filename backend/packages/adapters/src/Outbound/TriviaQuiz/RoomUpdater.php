<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuizRoomRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomUpdater as RoomUpdaterInterface;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use Psr\Log\LoggerInterface;

final readonly class RoomUpdater implements RoomUpdaterInterface
{
    public function __construct(
        private TriviaQuizRoomRepository $quizRoomRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws UpdateFailedException
     */
    #[\Override]
    public function update(TriviaQuizRoom $quizRoom): void
    {
        try {
            $this->logger->info('Start updating quiz room.', [
                'quizRoomId' => $quizRoom->getId(),
            ]);
            $this->quizRoomRepository->flush();
            $this->logger->info('Updating quiz room was successful.', [
                'quizRoomId' => $quizRoom->getId(),
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Updating quiz room was not successful.', [
                'quizRoomId' => $quizRoom->getId(),
            ]);
            $this->logger->debug('Updating quiz room was not successful.', [
                'quizRoomId' => $quizRoom->getId(),
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw UpdateFailedException::updateFailed(TriviaQuizRoom::class);
        }
    }
}
