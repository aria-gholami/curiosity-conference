<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\User;

use CuriosityConference\Application\Port\Inbound\User\DeleteQuizRoomParticipantHandler as DeleteQuizRoomParticipantHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomByIdLoader;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomUpdater;
use CuriosityConference\Application\Port\Outbound\User\ByIdLoader as UserByIdLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\QuizRoomParticipant;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

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
        private HubInterface $hub,
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
        $quizRoom->removeTriviaParticipant($participant);
        $this->roomUpdater->update($quizRoom);
        $this->hub->publish(new Update(
            \sprintf('trivia/quiz/room/%s/participants', $quizRoom->getId()),
            \json_encode(['action' => 'remove', 'participantId' => $participant->getId()], \JSON_THROW_ON_ERROR),
        ));
    }
}
