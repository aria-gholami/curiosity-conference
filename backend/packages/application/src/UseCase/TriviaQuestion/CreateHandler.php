<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuestion;

use CuriosityConference\Application\Port\Inbound\TriviaQuestion\CreateHandler as CreateHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuestion\Persister as QuestionPersister;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\ByIdLoader;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaQuestion\Question;
use CuriosityConference\Domain\TriviaQuestion\QuestionCreateDetails;
use CuriosityConference\Domain\TriviaQuiz\Id;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class CreateHandler implements CreateHandlerInterface
{
    public function __construct(
        private ByIdLoader $byIdLoader,
        private QuestionPersister $questionPersister,
    ) {
    }

    /**
     * @throws EmptyStringException
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     */
    #[\Override]
    public function handle(QuestionCreateDetails $questionCreateDetails): string
    {
        $quiz = $this->byIdLoader->load(
            new Id($questionCreateDetails->quizId),
        );
        $question = new Question(
            $quiz,
            $questionCreateDetails->question,
            [
                $questionCreateDetails->answer1,
                $questionCreateDetails->answer2,
                $questionCreateDetails->answer3,
                $questionCreateDetails->answer4,
            ],
            $questionCreateDetails->correctAnswerIndex,
        );
        $questionEntity = $this->questionPersister->persist($question);

        return $questionEntity->getId();
    }
}
