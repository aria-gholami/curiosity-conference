<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuiz;

use CuriosityConference\Application\Port\Inbound\TriviaQuiz\ListHighscoreByRoomHandler as ListHighscoreByRoomHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\HighscoreCollectionByQuizRoomLoader;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomByIdLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\HighscoreCollection;
use CuriosityConference\Domain\TriviaQuiz\Id;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class ListHighscoreByRoomHandler implements ListHighscoreByRoomHandlerInterface
{
    public function __construct(
        private RoomByIdLoader $roomByIdLoader,
        private HighscoreCollectionByQuizRoomLoader $highscoreCollectionByQuizRoomLoader,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function handleAndGet(Id $quizRoomId): HighscoreCollection
    {
        $quizRoom = $this->roomByIdLoader->load($quizRoomId->value);

        return $this->highscoreCollectionByQuizRoomLoader->load($quizRoom);
    }
}
