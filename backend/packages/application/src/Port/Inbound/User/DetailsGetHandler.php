<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Model\Id;

interface DetailsGetHandler
{
    /**
     * @throws LoadEntityFailedException
     */
    public function handleAndGet(Id $id): User;
}
