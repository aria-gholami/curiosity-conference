<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\User;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\InvalidEmailException;
use CuriosityConference\Domain\Exception\InvalidPasswordException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\User\Model\User;

interface CreateHandler
{
    /**
     * @throws PersistFailedException
     * @throws EmptyStringException
     * @throws InvalidEmailException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     */
    public function handle(User $user): string;
}
