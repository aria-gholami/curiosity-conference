<?php

declare(strict_types=1);

namespace CuriosityConference\Core\Trivia;

use CuriosityConference\Core\Trivia\Exception\CouldNotSendTriviaModifiedDataException;

interface PageModifiedBackendSendHandler
{
    /**
     * @throws CouldNotSendTriviaModifiedDataException
     */
    public function handle(array $quizzes): void;
}
