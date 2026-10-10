<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuestion\Mocks;

use CuriosityConference\Application\Port\Inbound\TriviaQuestion\CreateHandler;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaQuestion\QuestionCreateDetails;

final class CreateHandlerMock implements CreateHandler
{
    public QuestionCreateDetails $inputQuestionCreateDetails;
    public string $outputQuestionId;
    public ?PersistFailedException $throwException = null;

    /**
     * @throws PersistFailedException
     */
    #[\Override]
    public function handle(QuestionCreateDetails $questionCreateDetails): string
    {
        $this->inputQuestionCreateDetails = $questionCreateDetails;
        if ($this->throwException instanceof PersistFailedException) {
            throw $this->throwException;
        }

        return $this->outputQuestionId;
    }
}
