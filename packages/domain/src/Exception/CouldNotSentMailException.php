<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Exception;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class CouldNotSentMailException extends \Exception
{
    public static function mailNotSent(string $identifier): self
    {
        return new self(
            \sprintf('Mail for "%s" was not sent successfully.', $identifier),
        );
    }
}
