<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Exception;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class UpsertFailedException extends ValidationException
{
    public static function upsertFailed(string $identifier): self
    {
        return new self(
            \sprintf('Upsert entity "%s" was not successful.', $identifier),
        );
    }
}
