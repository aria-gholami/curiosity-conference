<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaAnswer;
use CuriosityConference\Application\Port\Outbound\TriviaAnswer\ByIdLoader;

final class ByIdLoaderMock implements ByIdLoader
{
    public string $inputId;
    public TriviaAnswer $outputAnswer;

    #[\Override]
    public function load(string $id): TriviaAnswer
    {
        $this->inputId = $id;

        return $this->outputAnswer;
    }
}
