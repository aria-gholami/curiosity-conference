<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\User;

use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\User\Model\Id;

interface DeleteHandler
{
    /**
     * @throws DeleteFailedException
     */
    public function handle(Id $userId): void;
}
