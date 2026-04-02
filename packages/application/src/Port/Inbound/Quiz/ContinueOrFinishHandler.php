<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Quiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\ContinueOrFinishRequestBody;
use CuriosityConference\Domain\Quiz\ContinueOrFinishResponse;
use CuriosityConference\Domain\Quiz\Exception\CurrentQuestionNotFoundException;

interface ContinueOrFinishHandler
{
    /**
     * @throws CurrentQuestionNotFoundException
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     * @throws UpdateFailedException
     */
    public function handleAndGet(ContinueOrFinishRequestBody $continueOrFinishRequestBody): ContinueOrFinishResponse;
}
