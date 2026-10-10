<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuizRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\Loader as LoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\Quiz;
use CuriosityConference\Domain\TriviaQuiz\QuizCollection;
use Psr\Log\LoggerInterface;

final readonly class Loader implements LoaderInterface
{
    public function __construct(
        private TriviaQuizRepository $quizRepository,
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function load(): QuizCollection
    {
        try {
            $this->logger->info('Start loading quizes.');
            $quizEntityGroup = $this->quizRepository->findAll();
            $quizCollection = new QuizCollection($quizEntityGroup);
            if ($quizCollection->isEmpty()) {
                $this->logger->notice('QuizCollection is empty.');
                throw LoadEntityFailedException::loadFailed(Quiz::class);
            }

            $this->logger->info('Loading quizes was successful.');

            return $quizCollection;
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading quizes was not successful.');
            $this->logger->debug('Loading quizes was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(Quiz::class);
        }
    }
}
