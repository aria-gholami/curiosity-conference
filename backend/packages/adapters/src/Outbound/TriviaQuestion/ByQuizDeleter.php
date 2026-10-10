<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuestionRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuestion\ByQuizDeleter as ByQuizDeleterInterface;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use Psr\Log\LoggerInterface;

final readonly class ByQuizDeleter implements ByQuizDeleterInterface
{
    public function __construct(
        private TriviaQuestionRepository $questionRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws DeleteFailedException
     */
    #[\Override]
    public function delete(TriviaQuiz $quiz): void
    {
        try {
            $this->logger->info('Start deleting question by quiz.', [
                'id' => $quiz->getId(),
            ]);
            $questionCollection = $this->questionRepository->findBy([
                'triviaQuiz' => $quiz,
            ]);
            foreach ($questionCollection as $questionEntity) {
                $this->questionRepository->remove($questionEntity);
            }

            $this->logger->info('Deleting question by quiz was successful.', [
                'id' => $quiz->getId(),
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Deleting question by quiz was not successful.', [
                'id' => $quiz->getId(),
            ]);
            $this->logger->debug('Deleting question by quiz was not successful.', [
                'id' => $quiz->getId(),
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw DeleteFailedException::deleteFailed(TriviaQuestion::class);
        }
    }
}
