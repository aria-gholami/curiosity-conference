<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\QuestionnaireQuestion\Mocks;

use CuriosityConference\Application\Port\Outbound\QuestionnaireQuestion\QuestionResetter;
use CuriosityConference\Domain\Exception\DeleteFailedException;

final class QuestionResetterMock implements QuestionResetter
{
    public bool $resetWasCalled = false;
    public ?DeleteFailedException $throwException = null;

    /**
     * @throws DeleteFailedException
     */
    #[\Override]
    public function reset(): void
    {
        $this->resetWasCalled = true;
        if ($this->throwException instanceof DeleteFailedException) {
            throw $this->throwException;
        }
    }
}
