<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Exception;

final class PersistFailedException extends \Exception
{
    public static function persistFailed(string $identifier): self
    {
        return new self(
            \sprintf('Persist entity "%s" was not successful.', $identifier),
        );
    }
}
