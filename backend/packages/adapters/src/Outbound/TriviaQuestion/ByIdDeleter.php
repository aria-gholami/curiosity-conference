<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuestionRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuestion\ByIdDeleter as ByIdDeleterInterface;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;
use Psr\Log\LoggerInterface;

final readonly class ByIdDeleter implements ByIdDeleterInterface
{
    public function __construct(
        private TriviaQuestionRepository $questionRepository,
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function delete(Id $id): void
    {
        try {
            $this->logger->info('Start deleting question.', [
                'id' => $id->value,
            ]);
            $questionEntity = $this->questionRepository->findOneBy([
                'id' => $id->value,
            ]);
            if (!$questionEntity instanceof TriviaQuestion) {
                return;
            }

            $this->questionRepository->remove($questionEntity);

            $this->logger->info('Deleting question was successful.', [
                'id' => $id->value,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Deleting question was not successful.', [
                'id' => $id->value,
            ]);
            $this->logger->debug('Deleting question was not successful.', [
                'id' => $id->value,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw DeleteFailedException::deleteFailed(TriviaQuestion::class);
        }
    }
}
