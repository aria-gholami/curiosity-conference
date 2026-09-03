<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks;

use CuriosityConference\Application\Port\Inbound\Quiz\ListHighscoreByAllRoomsHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\AllRoomsHighscoreCollection;

final class ListHighscoreByAllRoomsHandlerMock implements ListHighscoreByAllRoomsHandler
{
    public AllRoomsHighscoreCollection $outputAllRoomsHighscoreCollection;
    public ?LoadEntityFailedException $throwException = null;

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function handleAndGet(): AllRoomsHighscoreCollection
    {
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }

        return $this->outputAllRoomsHighscoreCollection;
    }
}
