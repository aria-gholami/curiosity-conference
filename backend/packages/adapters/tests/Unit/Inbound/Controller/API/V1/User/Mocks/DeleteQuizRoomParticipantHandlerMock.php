<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks;

use CuriosityConference\Application\Port\Inbound\User\DeleteQuizRoomParticipantHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\QuizRoomParticipant;

final class DeleteQuizRoomParticipantHandlerMock implements DeleteQuizRoomParticipantHandler
{
    public QuizRoomParticipant $inputQuizRoomParticipant;
    public ?\Exception $throwException = null;

    /**
     * @throws LoadEntityFailedException
     * @throws UpdateFailedException
     */
    #[\Override]
    public function handle(QuizRoomParticipant $quizRoomParticipant): void
    {
        $this->inputQuizRoomParticipant = $quizRoomParticipant;
        if ($this->throwException instanceof UpdateFailedException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }
    }
}
