<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Question;

use CuriosityConference\Application\Port\Inbound\Question\ByQuizLoadHandler as ByQuizLoadHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Question\ByIdLoader as QuestionByIdLoader;
use CuriosityConference\Application\Port\Outbound\Quiz\ByIdLoader as QuizByIdLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Question\QuestionCollection;
use CuriosityConference\Domain\Quiz\Id;
use Psr\Log\LoggerInterface;

final readonly class ByQuizLoadHandler implements ByQuizLoadHandlerInterface
{
    public function __construct(
        private QuizByIdLoader $quizByIdLoader,
        private QuestionByIdLoader $questionByIdLoader,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function handle(Id $id): QuestionCollection
    {
        try {
            $quiz = $this->quizByIdLoader->load($id);
        } catch (LoadEntityFailedException $exception) {
            $this->logger->warning('Quiz by id is not found.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new QuestionCollection([]);
        }

        return $this->questionByIdLoader->load($quiz);
    }
}
