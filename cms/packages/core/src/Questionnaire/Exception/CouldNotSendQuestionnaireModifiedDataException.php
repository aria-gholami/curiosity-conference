<?php

declare(strict_types=1);

namespace CuriosityConference\Core\Questionnaire\Exception;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class CouldNotSendQuestionnaireModifiedDataException extends \Exception
{
    public const string EXCEPTION_MESSAGE = 'Questionnaire modified data could not be sent: %s';

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
