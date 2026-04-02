<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Quiz\Exception;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class CurrentQuestionNotFoundException extends \Exception
{
    public static function notFound(): self
    {
        return new self('No current question for quiz.');
    }
}
