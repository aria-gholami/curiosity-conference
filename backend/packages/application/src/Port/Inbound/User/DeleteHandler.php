<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\User;

use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Model\Id;

interface DeleteHandler
{
    /**
     * @throws DeleteFailedException
     * @throws LoadEntityFailedException
     * @throws \JsonException
     */
    public function handle(Id $userId): void;
}
