<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuizRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\ByIdLoader as ByIdLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;
use Psr\Log\LoggerInterface;

final readonly class ByIdLoader implements ByIdLoaderInterface
{
    public function __construct(
        private TriviaQuizRepository $quizRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(Id $id): TriviaQuiz
    {
        try {
            $this->logger->info('Start loading quiz by id.', [
                'id' => $id->value,
            ]);
            $quiz = $this->quizRepository->findOneBy(['id' => $id->value]);
            if (!$quiz instanceof TriviaQuiz) {
                throw LoadEntityFailedException::loadFailed(TriviaQuiz::class);
            }

            $this->logger->info('Loading quiz by id was successful.', [
                'id' => $id->value,
            ]);

            return $quiz;
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading quiz by id was not successful.');
            $this->logger->debug('Loading quiz by id was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(TriviaQuiz::class);
        }
    }
}
