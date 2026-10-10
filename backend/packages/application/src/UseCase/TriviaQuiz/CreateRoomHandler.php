<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom as QuizRoomEntity;
use CuriosityConference\Application\Port\Inbound\TriviaQuiz\CreateRoomHandler as CreateRoomHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\ByIdLoader as QuizByIdLoader;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomPersister;
use CuriosityConference\Application\Port\Outbound\User\ByIdLoader as UserByIdLoader;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;
use CuriosityConference\Domain\TriviaQuiz\QuizRoom;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class CreateRoomHandler implements CreateRoomHandlerInterface
{
    public function __construct(
        private QuizByIdLoader $quizByIdLoader,
        private UserByIdLoader $userByIdLoader,
        private RoomPersister $quizRoomPersister,
    ) {
    }

    /**
     * @throws EmptyStringException
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     */
    #[\Override]
    public function handleAndGet(QuizRoom $quizRoom): QuizRoomEntity
    {
        $quiz = $this->quizByIdLoader->load(new Id($quizRoom->quizId));
        $host = $this->userByIdLoader->load($quizRoom->hostId);

        return $this->quizRoomPersister->persist($quiz, $host, $quizRoom->title, QuizRoomEntity::STATUS_CREATED);
    }
}
