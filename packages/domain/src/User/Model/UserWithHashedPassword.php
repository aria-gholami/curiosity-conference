<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\User\Model;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class UserWithHashedPassword
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $password,
    ) {
    }
}
