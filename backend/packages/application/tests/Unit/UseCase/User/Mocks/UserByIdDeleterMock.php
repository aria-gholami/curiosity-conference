<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks;

use CuriosityConference\Application\Port\Outbound\User\ByIdDeleter;
use CuriosityConference\Domain\User\Model\Id;

final class UserByIdDeleterMock implements ByIdDeleter
{
    public Id $inputUserId;

    public function delete(Id $userId): void
    {
        $this->inputUserId = $userId;
    }
}
