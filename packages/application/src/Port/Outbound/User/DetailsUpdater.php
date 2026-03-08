<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\User;

use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\UserDetails;

interface DetailsUpdater
{
    /**
     * @throws UpdateFailedException
     */
    public function update(UserDetails $userDetails): void;
}
