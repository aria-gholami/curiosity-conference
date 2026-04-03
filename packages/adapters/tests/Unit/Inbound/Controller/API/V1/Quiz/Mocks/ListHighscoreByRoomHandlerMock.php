<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks;

use CuriosityConference\Application\Port\Inbound\Quiz\ListHighscoreByRoomHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\HighscoreCollection;
use CuriosityConference\Domain\Quiz\Id;

final class ListHighscoreByRoomHandlerMock implements ListHighscoreByRoomHandler
{
    public Id $inputQuizRoomId;
    public HighscoreCollection $outputHighscoreCollection;
    public ?LoadEntityFailedException $throwException = null;

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function handleAndGet(Id $quizRoomId): HighscoreCollection
    {
        $this->inputQuizRoomId = $quizRoomId;
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }

        return $this->outputHighscoreCollection;
    }
}
