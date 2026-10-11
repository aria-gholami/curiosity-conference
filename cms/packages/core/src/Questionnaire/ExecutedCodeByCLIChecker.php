<?php

declare(strict_types=1);

namespace CuriosityConference\Core\Questionnaire;

interface ExecutedCodeByCLIChecker
{
    public function check(): bool;
}
