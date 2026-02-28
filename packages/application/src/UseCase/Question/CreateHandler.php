<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Question;

use CuriosityConference\Application\Port\Inbound\Question\CreateHandler as CreateHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Answer\Persister as AnswerPersister;
use CuriosityConference\Application\Port\Outbound\Question\Persister as QuestionPersister;
use CuriosityConference\Application\Port\Outbound\Quiz\ByIdLoader;
use CuriosityConference\Domain\Answer\Answer;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Question\Question;
use CuriosityConference\Domain\Question\QuestionCreateDetails;
use CuriosityConference\Domain\Quiz\Id;

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
        private AnswerPersister $answerPersister,
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
        );
        $questionEntity = $this->questionPersister->persist($question);
        $answer = new Answer(
            $questionEntity,
            [
                $questionCreateDetails->answer1,
                $questionCreateDetails->answer2,
                $questionCreateDetails->answer3,
                $questionCreateDetails->answer4,
            ],
            $questionCreateDetails->correctAnswerIndex,
        );
        $this->answerPersister->persist($answer);

        return $questionEntity->getId();
    }
}
