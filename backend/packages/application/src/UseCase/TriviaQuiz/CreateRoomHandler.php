<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom as QuizRoomEntity;
use CuriosityConference\Application\Port\Inbound\TriviaQuiz\CreateRoomHandler as CreateRoomHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomPersister;
use CuriosityConference\Application\Port\Outbound\User\ByIdLoader as UserByIdLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaQuiz\QuizRoom;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class CreateRoomHandler implements CreateRoomHandlerInterface
{
    public function __construct(
        private UserByIdLoader $userByIdLoader,
        private RoomPersister $quizRoomPersister,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     */
    #[\Override]
    public function handleAndGet(QuizRoom $quizRoom): QuizRoomEntity
    {
        $host = $this->userByIdLoader->load($quizRoom->hostId);

        return $this->quizRoomPersister->persist($host, $quizRoom->title, QuizRoomEntity::STATUS_CREATED);
    }
}
