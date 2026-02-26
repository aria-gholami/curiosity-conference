<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\User;

use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\User\Model\Email;

interface ByEmailDeleter
{
    /**
     * @throws DeleteFailedException
     */
    public function delete(Email $email): void;
}
