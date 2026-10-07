<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Quiz;

use CuriosityConference\Domain\Answer\Exception\CorrectAnswerNotExistingException;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;

interface TriviaPersistHandler
{
    /**
     * @throws CorrectAnswerNotExistingException
     * @throws DeleteFailedException
     * @throws EmptyStringException
     * @throws PersistFailedException
     * @throws LoadEntityFailedException
     */
    public function handle(array $quizzes): void;
}
