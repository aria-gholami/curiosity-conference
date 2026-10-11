<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber\Mocks;

use CuriosityConference\Core\Questionnaire\ExecutedCodeByCLIChecker;

final class QuestionnaireExecutedCodeByCLICheckerMock implements ExecutedCodeByCLIChecker
{
    public bool $outputExecutedByCLI;

    public function check(): bool
    {
        return $this->outputExecutedByCLI;
    }
}
