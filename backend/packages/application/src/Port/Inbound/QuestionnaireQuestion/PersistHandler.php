<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\QuestionnaireQuestion;

use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\QuestionnaireQuestion\Exception\EmptyAnswerArrayException;

interface PersistHandler
{
    /**
     * @throws DeleteFailedException
     * @throws EmptyStringException
     * @throws EmptyAnswerArrayException
     * @throws PersistFailedException
     */
    public function handle(array $questions): void;
}
