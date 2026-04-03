<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Exception;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class DeleteFailedException extends \Exception
{
    public static function deleteFailed(string $identifier): self
    {
        return new self(
            \sprintf('Delete entity "%s" was not successful.', $identifier),
        );
    }
}
