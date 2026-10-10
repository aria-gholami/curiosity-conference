<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz\Mocks;

use CuriosityConference\Application\Port\Outbound\TriviaQuiz\ByIdDeleter;
use CuriosityConference\Domain\TriviaQuiz\Id;

final class ByIdDeleterMock implements ByIdDeleter
{
    public array $inputId;

    #[\Override]
    public function delete(Id $id): void
    {
        $this->inputId[] = $id;
    }
}
