<?php

declare(strict_types=1);

namespace CuriosityConference\Core\Trivia;

interface ExecutedCodeByCLIChecker
{
    public function check(): bool;
}
