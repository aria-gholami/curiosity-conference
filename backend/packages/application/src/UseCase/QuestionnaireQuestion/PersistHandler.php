<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\QuestionnaireQuestion;

use CuriosityConference\Application\Port\Inbound\QuestionnaireQuestion\PersistHandler as PersistHandlerInterface;
use CuriosityConference\Application\Port\Outbound\QuestionnaireAnswer\Persister;
use CuriosityConference\Application\Port\Outbound\QuestionnaireQuestion\QuestionResetter;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\QuestionnaireQuestion\Exception\EmptyAnswerArrayException;
use CuriosityConference\Domain\QuestionnaireQuestion\Question;

final readonly class PersistHandler implements PersistHandlerInterface
{
    public function __construct(
        private QuestionResetter $questionResetter,
        private Persister $persister,
    ) {
    }

    /**
     * @throws DeleteFailedException
     * @throws EmptyAnswerArrayException
     * @throws EmptyStringException
     * @throws PersistFailedException
     */
    #[\Override]
    public function handle(array $questions): void
    {
        $this->questionResetter->reset();
        foreach ($questions as $question) {
            $questionnaireQuestion = new Question($question['question'] ?? '', $question['answers'] ?? []);
            $this->persister->persist($questionnaireQuestion);
        }
    }
}
