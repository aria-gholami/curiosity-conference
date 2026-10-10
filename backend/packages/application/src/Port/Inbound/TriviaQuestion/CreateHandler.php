<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\TriviaQuestion;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaQuestion\QuestionCreateDetails;

interface CreateHandler
{
    /**
     * @throws EmptyStringException
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     */
    public function handle(QuestionCreateDetails $questionCreateDetails): string;
}
