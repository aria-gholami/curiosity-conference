<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Application\Port\Inbound\TriviaQuiz\ActiveRoomLoadHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Model\Id;

final class ActiveRoomLoadHandlerMock implements ActiveRoomLoadHandler
{
    public Id $inputUserId;
    public ?TriviaQuizRoom $outputQuizRoom;
    public ?LoadEntityFailedException $throwException = null;

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function handleAndGet(Id $userId): ?TriviaQuizRoom
    {
        $this->inputUserId = $userId;
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }

        return $this->outputQuizRoom;
    }
}
