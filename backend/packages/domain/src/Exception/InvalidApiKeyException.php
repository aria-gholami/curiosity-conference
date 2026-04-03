<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Exception;

use CuriosityConference\Application\UseCase\Security\ApiKeyValidator;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class InvalidApiKeyException extends \Exception
{
    private const string MESSAGE_TEMPLATE = 'Authentication failed with error: "%s"';

    public static function headerIsMissing(): self
    {
        return new self(
            \sprintf(
                self::MESSAGE_TEMPLATE,
                \sprintf(
                    'Header "%s" is not set.',
                    ApiKeyValidator::HEADER_NAME,
                ),
            ),
        );
    }

    public static function invalidApiKey(): self
    {
        return new self(\sprintf(self::MESSAGE_TEMPLATE, 'Api key is invalid.'));
    }
}
