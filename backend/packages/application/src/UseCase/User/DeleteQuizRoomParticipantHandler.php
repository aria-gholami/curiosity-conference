<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\User;

use CuriosityConference\Application\Port\Inbound\User\DeleteQuizRoomParticipantHandler as DeleteQuizRoomParticipantHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByIdLoader;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomUpdater;
use CuriosityConference\Application\Port\Outbound\User\ByIdLoader as UserByIdLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\QuizRoomParticipant;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class DeleteQuizRoomParticipantHandler implements DeleteQuizRoomParticipantHandlerInterface
{
    public function __construct(
        private RoomByIdLoader $roomByIdLoader,
        private UserByIdLoader $userByIdLoader,
        private RoomUpdater $roomUpdater,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     * @throws UpdateFailedException
     */
    #[\Override]
    public function handle(QuizRoomParticipant $quizRoomParticipant): void
    {
        $quizRoom = $this->roomByIdLoader->load($quizRoomParticipant->quizRoomId);
        $participant = $this->userByIdLoader->load($quizRoomParticipant->participantId);
        $quizRoom->removeParticipant($participant);
        $this->roomUpdater->update($quizRoom);
    }
}
