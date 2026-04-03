<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\User;

use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\User\Model\UserName;

interface ByUserNameDeleter
{
    /**
     * @throws DeleteFailedException
     */
    public function delete(UserName $userName): void;
}
