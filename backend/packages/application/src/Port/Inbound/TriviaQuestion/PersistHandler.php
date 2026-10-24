<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\TriviaQuestion;

use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaAnswer\Exception\CorrectAnswerNotExistingException;

interface PersistHandler
{
    /**
     * @throws CorrectAnswerNotExistingException
     * @throws DeleteFailedException
     * @throws PersistFailedException
     */
    public function handle(array $questions): void;
}
