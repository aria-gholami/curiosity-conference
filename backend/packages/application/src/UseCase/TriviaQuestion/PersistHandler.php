<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuestion;

use CuriosityConference\Application\Port\Inbound\TriviaQuestion\PersistHandler as TriviaPersistHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuestion\Persister as QuestionPersister;
use CuriosityConference\Application\Port\Outbound\TriviaQuestion\Resetter;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaAnswer\Exception\CorrectAnswerNotExistingException;
use CuriosityConference\Domain\TriviaQuestion\Question;

final readonly class PersistHandler implements TriviaPersistHandlerInterface
{
    public function __construct(
        private Resetter $resetter,
        private QuestionPersister $questionPersister,
    ) {
    }

    /**
     * @throws CorrectAnswerNotExistingException
     * @throws DeleteFailedException
     * @throws PersistFailedException
     */
    #[\Override]
    public function handle(array $questions): void
    {
        $this->resetter->reset();
        foreach ($questions as $question) {
            $answers = $question['answers'];
            $correctAnswerIndex = $this->getCorrectAnswerIndex($answers);
            $question = new Question(
                $question['question'],
                \array_column($answers, 'answer'),
                $correctAnswerIndex,
            );
            $this->questionPersister->persist($question);
        }
    }

    /**
     * @throws CorrectAnswerNotExistingException
     */
    private function getCorrectAnswerIndex(array $answers): int
    {
        $correctAnswerIndex = null;
        foreach ($answers as $index => $answer) {
            /** @var ?bool $isCorrect */
            $isCorrect = $answer['correct'];
            if ($isCorrect ?? false) {
                $correctAnswerIndex = $index;
                break;
            }
        }
        if (!\is_int($correctAnswerIndex)) {
            throw new CorrectAnswerNotExistingException(
                'Question must have a correct answer.',
            );
        }

        return $correctAnswerIndex;
    }
}
