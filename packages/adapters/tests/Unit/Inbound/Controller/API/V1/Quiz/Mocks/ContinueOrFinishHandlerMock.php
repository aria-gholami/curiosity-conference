<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks;

use CuriosityConference\Application\Port\Inbound\Quiz\ContinueOrFinishHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\ContinueOrFinishRequestBody;
use CuriosityConference\Domain\Quiz\ContinueOrFinishResponse;
use CuriosityConference\Domain\Quiz\Exception\CurrentQuestionNotFoundException;

final class ContinueOrFinishHandlerMock implements ContinueOrFinishHandler
{
    public ContinueOrFinishRequestBody $inputHighscoreUpdateRequestBody;
    public ContinueOrFinishResponse $outputHighscoreUpdateResponse;
    public ?\Exception $throwException = null;

    /**
     * @throws CurrentQuestionNotFoundException
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     * @throws UpdateFailedException
     */
    #[\Override]
    public function handleAndGet(ContinueOrFinishRequestBody $continueOrFinishRequestBody): ContinueOrFinishResponse
    {
        $this->inputHighscoreUpdateRequestBody = $continueOrFinishRequestBody;
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof UpdateFailedException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof CurrentQuestionNotFoundException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof PersistFailedException) {
            throw $this->throwException;
        }

        return $this->outputHighscoreUpdateResponse;
    }
}
