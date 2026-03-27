<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRoomRepository;
use CuriosityConference\Application\Port\Outbound\Quiz\QuizRoomUpdater as QuizRoomUpdaterInterface;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Id;
use Psr\Log\LoggerInterface;

final readonly class QuizRoomUpdater implements QuizRoomUpdaterInterface
{
    public function __construct(
        private QuizRoomRepository $quizRoomRepository,
        private LoggerInterface    $logger,
    ) {
    }

    /**
     * @throws UpdateFailedException
     */
    #[\Override]
    public function updateIsStartedToTrue(Id $quizId): void
    {
        try {
            $this->logger->info('Start updating isStarted.', [
                'quizId' => $quizId->value,
            ]);
            $this->quizRoomRepository->updateIsStarted($quizId);
            $this->logger->info('Updating isStarted was successful.', [
                'quizId' => $quizId->value,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Updating isStarted was not successful.', [
                'quizId' => $quizId->value,
            ]);
            $this->logger->debug('Updating isStarted was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
                'quizId' => $quizId->value,
            ]);

            throw UpdateFailedException::updateFailed(Quiz::class);
        }
    }
}