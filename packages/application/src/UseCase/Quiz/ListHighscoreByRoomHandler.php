<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Application\Port\Inbound\Quiz\ListHighscoreByRoomHandler as ListHighscoreByRoomHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Quiz\HighscoreCollectionByQuizRoomLoader;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByIdLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\HighscoreCollection;
use CuriosityConference\Domain\Quiz\Id;

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
