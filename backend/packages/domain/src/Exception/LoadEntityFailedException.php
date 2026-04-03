<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Exception;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class LoadEntityFailedException extends \Exception
{
    public static function loadFailed(string $identifier): self
    {
        return new self(
            \sprintf('Load entity "%s" was not successful.', $identifier),
        );
    }
}
