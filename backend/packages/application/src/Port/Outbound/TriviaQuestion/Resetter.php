<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuestion;

use CuriosityConference\Domain\Exception\DeleteFailedException;

interface Resetter
{
    /**
     * @throws DeleteFailedException
     */
    public function reset(): void;
}
