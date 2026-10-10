<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\TriviaQuiz\Exception;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class QuizRoomIsAlreadyStartedException extends \Exception
{
    public static function alreadyStarted(): self
    {
        return new self('Quiz room is already started.');
    }
}
