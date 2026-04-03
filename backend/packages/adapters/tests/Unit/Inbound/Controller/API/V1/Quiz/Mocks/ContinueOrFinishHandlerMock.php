<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks;

use CuriosityConference\Application\Port\Inbound\Quiz\ContinueOrFinishHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Exception\CurrentQuestionNotFoundException;
use CuriosityConference\Domain\Quiz\Id;

final class ContinueOrFinishHandlerMock implements ContinueOrFinishHandler
{
    public Id $inputQuizRoomId;
    public ?\Exception $throwException = null;

    /**
     * @throws CurrentQuestionNotFoundException
     * @throws LoadEntityFailedException
     * @throws UpdateFailedException
     */
    #[\Override]
    public function handle(Id $quizRoomId): void
    {
        $this->inputQuizRoomId = $quizRoomId;
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof CurrentQuestionNotFoundException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof UpdateFailedException) {
            throw $this->throwException;
        }
    }
}
