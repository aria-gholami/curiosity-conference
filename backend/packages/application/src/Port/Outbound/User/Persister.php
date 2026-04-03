<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\User;

use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\User\Model\UserName;

interface Persister
{
    /**
     * @throws PersistFailedException
     */
    public function persist(UserName $userName): string;
}
