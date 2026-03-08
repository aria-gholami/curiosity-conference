<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\Security\JwtUserToken;

final class JwtUserTokenMock implements JwtUserToken
{
    public User $inputUser;
    public string $outputJwtToken;
    public string $inputJwtToken;
    public User $outputUser;

    #[\Override]
    public function encode(User $user): string
    {
        $this->inputUser = $user;

        return $this->outputJwtToken;
    }

    #[\Override]
    public function decode(string $token): User
    {
        $this->inputJwtToken = $token;

        return $this->outputUser;
    }
}
