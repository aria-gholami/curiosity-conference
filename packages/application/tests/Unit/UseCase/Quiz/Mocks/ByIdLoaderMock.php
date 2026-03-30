<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Answer;
use CuriosityConference\Application\Port\Outbound\Answer\ByIdLoader;

final class ByIdLoaderMock implements ByIdLoader
{
    public string $inputId;
    public Answer $outputAnswer;

    #[\Override]
    public function load(string $id): Answer
    {
        $this->inputId = $id;

        return $this->outputAnswer;
    }
}
