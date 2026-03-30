<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks;

use CuriosityConference\Application\Port\Inbound\Quiz\HighscoreUpdateHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\HighscoreUpdateRequestBody;
use CuriosityConference\Domain\Quiz\HighscoreUpdateResponse;

final class HighscoreUpdateHandlerMock implements HighscoreUpdateHandler
{
    public HighscoreUpdateRequestBody $inputHighscoreUpdateRequestBody;
    public HighscoreUpdateResponse $outputHighscoreUpdateResponse;
    public ?\Exception $throwException = null;

    /**
     * @throws LoadEntityFailedException
     * @throws UpdateFailedException
     */
    #[\Override]
    public function handleAndGet(HighscoreUpdateRequestBody $highscoreUpdateRequestBody): HighscoreUpdateResponse
    {
        $this->inputHighscoreUpdateRequestBody = $highscoreUpdateRequestBody;
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof UpdateFailedException) {
            throw $this->throwException;
        }

        return $this->outputHighscoreUpdateResponse;
    }
}
