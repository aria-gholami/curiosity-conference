<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuizRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\ByIdDeleter as ByIdLoaderInterface;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;
use Psr\Log\LoggerInterface;

final readonly class ByIdDeleter implements ByIdLoaderInterface
{
    public function __construct(
        private TriviaQuizRepository $quizRepository,
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
            if (!$quizEntity instanceof TriviaQuiz) {
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

            throw DeleteFailedException::deleteFailed(TriviaQuiz::class);
        }
    }
}
