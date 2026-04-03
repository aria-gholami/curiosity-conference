<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\User;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\User\Model\Id;
use CuriosityConference\Domain\User\Model\UserName;

interface CreateHandler
{
    /**
     * @throws PersistFailedException
     * @throws EmptyStringException
     */
    public function handle(UserName $userName): Id;
}
