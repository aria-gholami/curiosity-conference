<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Security;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;

interface JwtUserToken
{
    public function encode(User $user): string;

    /**
     * @throws \InvalidArgumentException
     * @throws \DomainException
     * @throws \UnexpectedValueException
     * @throws BeforeValidException
     * @throws ExpiredException
     */
    public function decode(string $token): User;
}
