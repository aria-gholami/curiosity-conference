<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\User;

use CuriosityConference\Application\Port\Inbound\User\CreateHandler as CreateHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Security\Argon2idHasher;
use CuriosityConference\Application\Port\Outbound\User\Persister;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\InvalidEmailException;
use CuriosityConference\Domain\Exception\InvalidPasswordException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\User\Model\User;
use CuriosityConference\Domain\User\Model\UserWithHashedPassword;

final readonly class CreateHandler implements CreateHandlerInterface
{
    public function __construct(
        private Persister $userPersister,
        private Argon2idHasher $hashGenerator,
    ) {
    }

    /**
     * @throws EmptyStringException
     * @throws InvalidEmailException
     * @throws PersistFailedException
     * @throws InvalidPasswordException
     */
    #[\Override]
    public function handle(User $user): string
    {
        $passwordHash = $this->hashGenerator->hash($user->password);
        $userWithHashedPassword = new UserWithHashedPassword(
            $user->firstName,
            $user->lastName,
            $user->email,
            $passwordHash,
        );

        return $this->userPersister->persist($userWithHashedPassword);
    }
}
