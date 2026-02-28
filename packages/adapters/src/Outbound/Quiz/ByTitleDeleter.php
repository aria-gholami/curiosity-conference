<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRepository;
use CuriosityConference\Application\Port\Outbound\Quiz\ByTitleDeleter as ByTitleDeleterInterface;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Quiz\Id;
use Psr\Log\LoggerInterface;

final readonly class ByTitleDeleter implements ByTitleDeleterInterface
{
    public function __construct(
        private QuizRepository $quizRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws DeleteFailedException
     */
    #[\Override]
    public function delete(Id $id): void
    {
        try {
            $this->logger->info('Start deleting quiz.', [
                'id' => $id->value,
            ]);
            $quizEntity = $this->quizRepository->findOneBy([
                'id' => $id->value,
            ]);
            if (!$quizEntity instanceof Quiz) {
                return;
            }

            $this->quizRepository->remove($quizEntity);

            $this->logger->info('Deleting quiz was successful.', [
                'id' => $id->value,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Deleting quiz was not successful.', [
                'id' => $id->value,
            ]);
            $this->logger->debug('Deleting quiz was not successful.', [
                'id' => $id->value,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw DeleteFailedException::deleteFailed(Quiz::class);
        }
    }
}
