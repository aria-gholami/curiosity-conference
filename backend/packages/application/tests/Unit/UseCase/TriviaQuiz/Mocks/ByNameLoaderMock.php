<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\ByNameLoader;

final class ByNameLoaderMock implements ByNameLoader
{
    public string $inputTitle;
    public array $outputQuiz;

    #[\Override]
    public function load(string $title): ?TriviaQuiz
    {
        $this->inputTitle = $title;

        return \array_pop($this->outputQuiz);
    }
}
