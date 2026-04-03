<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\User;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\QuizRoomParticipant;

interface DeleteQuizRoomParticipantHandler
{
    /**
     * @throws LoadEntityFailedException
     * @throws UpdateFailedException
     */
    public function handle(QuizRoomParticipant $quizRoomParticipant): void;
}
