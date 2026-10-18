<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\QuestionnaireQuestion;

use CuriosityConference\Domain\Exception\DeleteFailedException;

interface QuestionResetter
{
    /**
     * @throws DeleteFailedException
     */
    public function reset(): void;
}
