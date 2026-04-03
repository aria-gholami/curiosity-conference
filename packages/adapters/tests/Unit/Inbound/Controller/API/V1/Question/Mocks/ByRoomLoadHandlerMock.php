<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Question\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
use CuriosityConference\Application\Port\Inbound\Question\ByRoomLoadHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\Id;

final class ByRoomLoadHandlerMock implements ByRoomLoadHandler
{
    public Id $inputId;
    public Question $outputQuestion;
    public ?LoadEntityFailedException $throwException = null;

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function handleAndGet(Id $id): Question
    {
        $this->inputId = $id;
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }

        return $this->outputQuestion;
    }
}
