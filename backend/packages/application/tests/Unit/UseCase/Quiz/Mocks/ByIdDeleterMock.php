<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks;

use CuriosityConference\Application\Port\Outbound\Quiz\ByIdDeleter;
use CuriosityConference\Domain\Quiz\Id;

final class ByIdDeleterMock implements ByIdDeleter
{
    public array $inputId;

    #[\Override]
    public function delete(Id $id): void
    {
        $this->inputId[] = $id;
    }
}
