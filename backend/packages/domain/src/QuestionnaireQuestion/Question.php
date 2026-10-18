<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\QuestionnaireQuestion;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\QuestionnaireQuestion\Exception\EmptyAnswerArrayException;

final readonly class Question
{
    /**
     * @throws EmptyAnswerArrayException
     * @throws EmptyStringException
     */
    public function __construct(
        public string $title,
        public array $answers,
    ) {
        if ('' === $this->title) {
            throw EmptyStringException::stringIsEmpty('questionnaireQuestionTitle');
        }
        if (0 === \count($this->answers)) {
            throw EmptyAnswerArrayException::isInvalid('questionnaireQuestionAnswers');
        }
    }
}
