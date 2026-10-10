<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuestionRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuestion\ByIdLoader as ByIdLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuestion\QuestionCollection;
use Psr\Log\LoggerInterface;

final readonly class ByIdLoader implements ByIdLoaderInterface
{
    public function __construct(
        private TriviaQuestionRepository $questionRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(TriviaQuiz $quiz): QuestionCollection
    {
        try {
            $this->logger->info('Start loading questions by quiz id.', [
                'quizId' => $quiz->getId(),
            ]);
            $questionGroup = $this->questionRepository->findBy(['triviaQuiz' => $quiz]);
            $this->logger->info('Loading questions by quiz id was successful.', [
                'quizId' => $quiz->getId(),
            ]);

            return new QuestionCollection($questionGroup);
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading questions by quiz id was not successful.', [
                'quizId' => $quiz->getId(),
            ]);
            $this->logger->debug('Loading questions by quiz id was not successful.', [
                'quizId' => $quiz->getId(),
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(TriviaQuestion::class);
        }
    }
}
