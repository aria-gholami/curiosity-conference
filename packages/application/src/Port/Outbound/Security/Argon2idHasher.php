<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Security;

interface Argon2idHasher
{
    public function hash(string $value): string;

    public function verify(string $plainPassword, string $hash): bool;
}
