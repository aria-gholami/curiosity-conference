<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Application\Port\Inbound\Quiz\JoinRoomHandler as JoinRoomHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByIdLoader;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomParticipantPersister;
use CuriosityConference\Application\Port\Outbound\User\ByIdLoader as UserByIdLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\Exception\QuizRoomIsAlreadyStartedException;
use CuriosityConference\Domain\Quiz\JoinQuizRoom;

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
    public function handleAndGet(JoinQuizRoom $joinQuizRoom): string
    {
        $quizRoom = $this->roomByIdLoader->load($joinQuizRoom->quizRoomId);
        if (QuizRoom::STATUS_CREATED !== $quizRoom->getStatus()) {
            throw QuizRoomIsAlreadyStartedException::alreadyStarted();
        }

        $user = $this->userByIdLoader->load($joinQuizRoom->userId);
        $quizRoom->addParticipant($user);

        return $this->roomParticipantPersister->persist($quizRoom);
    }
}
