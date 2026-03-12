<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Security;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\Security\JwtUserToken as JwtUserTokenInterface;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class JwtUserToken implements JwtUserTokenInterface
{
    public function __construct(
        private string $secret,
    ) {
    }

    #[\Override]
    public function encode(User $user): string
    {
        $payload = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \DomainException
     * @throws \UnexpectedValueException
     * @throws BeforeValidException
     * @throws ExpiredException
     */
    #[\Override]
    public function decode(string $token): User
    {
        $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));

        return new User(
            $decoded->id,
            $decoded->firstName,
            $decoded->lastName,
            $decoded->email,
            '',
        );
    }
}
