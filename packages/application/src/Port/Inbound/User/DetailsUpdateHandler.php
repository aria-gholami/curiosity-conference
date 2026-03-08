<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\User;

use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\UserDetails;

interface DetailsUpdateHandler
{
    /**
     * @throws UpdateFailedException
     */
    public function handle(UserDetails $userDetails): void;
}
