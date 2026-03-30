<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRoomRepository;
use CuriosityConference\Application\Port\Outbound\Quiz\QuizRoomUpdater as QuizRoomUpdaterInterface;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Id;
use Psr\Log\LoggerInterface;

final readonly class QuizRoomUpdater implements QuizRoomUpdaterInterface
{
    public function __construct(
        private QuizRoomRepository $quizRoomRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws UpdateFailedException
     */
    #[\Override]
    public function updateStatus(Id $quizRoomId, string $status): void
    {
        try {
            $this->logger->info('Start updating status.', [
                'quizRoomId' => $quizRoomId->value,
                'status' => $status,
            ]);
            $this->quizRoomRepository->updateStatus($quizRoomId, $status);
            $this->logger->info('Updating status was successful.', [
                'quizRoomId' => $quizRoomId->value,
                'status' => $status,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Updating status was not successful.', [
                'quizRoomId' => $quizRoomId->value,
                'status' => $status,
            ]);
            $this->logger->debug('Updating status was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
                'status' => $status,
                'quizRoomId' => $quizRoomId->value,
            ]);

            throw UpdateFailedException::updateFailed(QuizRoom::class);
        }
    }
}
