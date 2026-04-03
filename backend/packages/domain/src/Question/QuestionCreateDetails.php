<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Question;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\InvalidNumberException;

final readonly class QuestionCreateDetails
{
    /**
     * @throws EmptyStringException
     * @throws InvalidNumberException
     */
    public function __construct(
        public string $question,
        public string $quizId,
        public string $answer1,
        public string $answer2,
        public string $answer3,
        public string $answer4,
        public int $correctAnswerIndex,
    ) {
        if ('' === $this->question) {
            throw EmptyStringException::stringIsEmpty('questionCreateDetailsQuestion');
        }
        if ('' === $this->quizId) {
            throw EmptyStringException::stringIsEmpty('questionCreateDetailsQuizId');
        }
        if ('' === $this->answer1) {
            throw EmptyStringException::stringIsEmpty('questionCreateDetailsAnswer1');
        }
        if ('' === $this->answer2) {
            throw EmptyStringException::stringIsEmpty('questionCreateDetailsAnswer2');
        }
        if ('' === $this->answer3) {
            throw EmptyStringException::stringIsEmpty('questionCreateDetailsAnswer3');
        }
        if ('' === $this->answer4) {
            throw EmptyStringException::stringIsEmpty('questionCreateDetailsAnswer4');
        }
        if (-1 === $this->correctAnswerIndex) {
            throw InvalidNumberException::intIsInvalid('questionCreateDetailsCorrectAnswerIndex');
        }
    }
}
