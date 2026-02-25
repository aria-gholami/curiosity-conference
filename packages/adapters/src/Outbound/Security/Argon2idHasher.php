<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Security;

use CuriosityConference\Application\Port\Outbound\Security\Argon2idHasher as Argon2idHasherInterface;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class Argon2idHasher implements Argon2idHasherInterface
{
    #[\Override]
    public function hash(string $value): string
    {
        return \password_hash($value, \PASSWORD_ARGON2ID);
    }

    #[\Override]
    public function verify(string $plainPassword, string $hash): bool
    {
        return \password_verify($plainPassword, $hash);
    }
}
