<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Security;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;

interface JwtUserToken
{
    public function encode(User $user): string;

    public function decode(string $token): User;
}
