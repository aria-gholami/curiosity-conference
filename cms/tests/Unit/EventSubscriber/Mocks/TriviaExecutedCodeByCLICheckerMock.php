<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber\Mocks;

use CuriosityConference\Core\Trivia\ExecutedCodeByCLIChecker;

final class TriviaExecutedCodeByCLICheckerMock implements ExecutedCodeByCLIChecker
{
    public bool $outputExecutedByCLI;

    public function check(): bool
    {
        return $this->outputExecutedByCLI;
    }
}
