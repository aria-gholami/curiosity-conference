<?php

declare(strict_types=1);

namespace App\Questionnaire;

use CuriosityConference\Core\Questionnaire\ExecutedCodeByCLIChecker as ExecutedCodeByCLICheckerInterface;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class ExecutedCodeByCLIChecker implements ExecutedCodeByCLICheckerInterface
{
    #[\Override]
    public function check(): bool
    {
        return \PHP_SAPI === 'cli';
    }
}
