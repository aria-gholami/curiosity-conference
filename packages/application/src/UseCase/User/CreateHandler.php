<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\User;

use CuriosityConference\Application\Port\Inbound\User\CreateHandler as CreateHandlerInterface;
use CuriosityConference\Application\Port\Outbound\User\Persister;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\User\Model\Id;
use CuriosityConference\Domain\User\Model\UserName;

final readonly class CreateHandler implements CreateHandlerInterface
{
    public function __construct(
        private Persister $userPersister,
    ) {
    }

    /**
     * @throws PersistFailedException
     * @throws EmptyStringException
     */
    #[\Override]
    public function handle(UserName $userName): Id
    {
        $id = $this->userPersister->persist($userName);

        return new Id(
            $id,
        );
    }
}
