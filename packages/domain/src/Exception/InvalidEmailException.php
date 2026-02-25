<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Exception;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class InvalidEmailException extends ValidationException
{
    public static function emailIsInvalid(string $identifier): self
    {
        return new self(
            parent::buildMessage(
                $identifier,
                \sprintf('Email from "%s" is invalid.', $identifier),
            ),
        );
    }
}
