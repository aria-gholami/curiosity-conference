<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuestion\Mocks;

use CuriosityConference\Application\Port\Outbound\TriviaQuestion\Resetter;

final class ResetterMock implements Resetter
{
    public bool $resetCalled = false;

    #[\Override]
    public function reset(): void
    {
        $this->resetCalled = true;
    }
}
