<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\User\Model;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class UserLoginResponse
{
    public function __construct(
        public string $id,
        public string $token,
        public bool $isAdmin,
    ) {
    }
}
