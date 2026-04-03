<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\User;

use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\User\Model\UserName;

interface DeleteHandler
{
    /**
     * @throws DeleteFailedException
     */
    public function handle(UserName $userName): void;
}
