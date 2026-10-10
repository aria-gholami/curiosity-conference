<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Application\Port\Inbound\TriviaQuiz\JoinRoomHandler as JoinRoomHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomByIdLoader;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomParticipantPersister;
use CuriosityConference\Application\Port\Outbound\User\ByIdLoader as UserByIdLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaQuiz\Exception\QuizRoomIsAlreadyStartedException;
use CuriosityConference\Domain\TriviaQuiz\JoinQuizRoom;

final readonly class JoinRoomHandler implements JoinRoomHandlerInterface
{
    public function __construct(
        private RoomByIdLoader $roomByIdLoader,
        private UserByIdLoader $userByIdLoader,
        private RoomParticipantPersister $roomParticipantPersister,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     * @throws QuizRoomIsAlreadyStartedException
     */
    #[\Override]
    public function handleAndGet(JoinQuizRoom $joinQuizRoom): TriviaQuizRoom
    {
        $quizRoom = $this->roomByIdLoader->load($joinQuizRoom->quizRoomId);
        if (TriviaQuizRoom::STATUS_CREATED !== $quizRoom->getStatus()) {
            throw QuizRoomIsAlreadyStartedException::alreadyStarted();
        }

        $user = $this->userByIdLoader->load($joinQuizRoom->userId);
        $quizRoom->addTriviaParticipant($user);

        return $this->roomParticipantPersister->persist($quizRoom);
    }
}
