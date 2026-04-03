<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Question;

use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Quiz\Id;

interface DeleteHandler
{
    /**
     * @throws DeleteFailedException
     */
    public function handle(Id $id): void;
}
