<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\QuestionnaireQuestion\Mocks;

use CuriosityConference\Application\Port\Outbound\QuestionnaireSettings\Upserter;
use CuriosityConference\Domain\Exception\UpsertFailedException;

final class UpserterMock implements Upserter
{
    public bool $inputShowResults;
    public ?UpsertFailedException $throwException = null;

    /**
     * @throws UpsertFailedException
     */
    #[\Override]
    public function upsert(bool $showResults): void
    {
        $this->inputShowResults = $showResults;
        if ($this->throwException instanceof UpsertFailedException) {
            throw $this->throwException;
        }
    }
}
