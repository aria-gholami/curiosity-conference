<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Quiz;

use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Quiz\Title;

interface DeleteHandler
{
    /**
     * @throws DeleteFailedException
     */
    public function handle(Title $title): void;
}
