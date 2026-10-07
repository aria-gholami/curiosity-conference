<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Application\Port\Outbound\Quiz\ByNameLoader;

final class ByNameLoaderMock implements ByNameLoader
{
    public string $inputTitle;
    public array $outputQuiz;

    #[\Override]
    public function load(string $title): ?Quiz
    {
        $this->inputTitle = $title;

        return \array_pop($this->outputQuiz);
    }
}
