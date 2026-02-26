<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRepository;
use CuriosityConference\Application\Port\Outbound\Quiz\ByTitleDeleter as ByTitleDeleterInterface;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Quiz\Title;
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
    public function delete(Title $title): void
    {
        try {
            $this->logger->info('Start deleting quiz.', [
                'title' => $title->value,
            ]);
            $quizEntity = $this->quizRepository->findOneBy([
                'title' => $title->value,
            ]);
            if (!$quizEntity instanceof Quiz) {
                return;
            }

            $this->quizRepository->remove($quizEntity);

            $this->logger->info('Deleting quiz was successful.', [
                'title' => $title->value,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Deleting quiz was not successful.', [
                'title' => $title->value,
            ]);
            $this->logger->debug('Deleting quiz was not successful.', [
                'title' => $title->value,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw DeleteFailedException::deleteFailed(Quiz::class);
        }
    }
}
