<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Application\Port\Inbound\Quiz\ListHighscoreByAllRoomsHandler as ListHighscoreByAllRoomsHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Quiz\HighscoreByAllRoomsLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\AllRoomsHighscoreCollection;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class ListHighscoreByAllRoomsHandler implements ListHighscoreByAllRoomsHandlerInterface
{
    public function __construct(
        private HighscoreByAllRoomsLoader $highscoreByAllRoomLoader,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function handleAndGet(): AllRoomsHighscoreCollection
    {
        return $this->highscoreByAllRoomLoader->load();
    }
}
