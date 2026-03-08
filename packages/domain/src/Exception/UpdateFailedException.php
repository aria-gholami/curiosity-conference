<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Exception;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class UpdateFailedException extends \Exception
{
    public static function updateFailed(string $identifier): self
    {
        return new self(
            \sprintf('Update entity "%s" was not successful.', $identifier),
        );
    }
}
