<?php

declare(strict_types=1);

namespace CuriosityConference\Core\Trivia\Exception;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class CouldNotSendTriviaModifiedDataException extends \Exception
{
    public const string EXCEPTION_MESSAGE = 'Trivia modified data could not be sent: %s';

    public static function sendFailed(string $exceptionMessage): self
    {
        return new self(
            \sprintf(
                self::EXCEPTION_MESSAGE,
                $exceptionMessage,
            ),
        );
    }
}
