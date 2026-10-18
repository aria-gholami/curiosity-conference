<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\QuestionnaireSettings;

use CuriosityConference\Domain\Exception\UpsertFailedException;

interface Upserter
{
    /**
     * @throws UpsertFailedException
     */
    public function upsert(bool $showResults): void;
}
