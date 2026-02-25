<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks;

use CuriosityConference\Application\Port\Outbound\User\Persister;
use CuriosityConference\Domain\User\Model\User;

final class PersisterMock implements Persister
{
    public User $inputUser;
    public string $outputUserId;

    #[\Override]
    public function persist(User $user): string
    {
        $this->inputUser = $user;

        return $this->outputUserId;
    }
}
