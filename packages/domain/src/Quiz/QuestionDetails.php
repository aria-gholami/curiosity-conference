<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Quiz;

final readonly class QuestionDetails
{
    public function __construct(
        public string $question,
        public string $answer1,
        public string $answer2,
        public string $answer3,
        public string $answer4,
        public int $correctAnswerIndex,
    ) {
    }
}
