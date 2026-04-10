<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\User;

use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\User\Model\Id;

interface ByIdDeleter
{
    /**
     * @throws DeleteFailedException
     */
    public function delete(Id $userId): void;
}
