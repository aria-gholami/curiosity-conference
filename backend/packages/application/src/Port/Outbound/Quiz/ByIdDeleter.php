<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Quiz;

use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Quiz\Id;

interface ByIdDeleter
{
    /**
     * @throws DeleteFailedException
     */
    public function delete(Id $id): void;
}
