<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Exception;

use CuriosityConference\Application\UseCase\Security\JwtValidator;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class InvalidJwtException extends \Exception
{
    private const string MESSAGE_TEMPLATE = 'Authentication failed with error: "%s"';

    public static function headerIsMissing(): self
    {
        return new self(
            \sprintf(
                self::MESSAGE_TEMPLATE,
                \sprintf(
                    'Header "%s" is not set.',
                    JwtValidator::HEADER_NAME,
                ),
            ),
        );
    }

    public static function invalidJwt(): self
    {
        return new self(\sprintf(self::MESSAGE_TEMPLATE, 'Jwt is invalid.'));
    }
}
