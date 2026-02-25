<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks;

use CuriosityConference\Application\Port\Outbound\User\Persister;
use CuriosityConference\Domain\User\Model\UserWithHashedPassword;

final class PersisterMock implements Persister
{
    public UserWithHashedPassword $inputUser;
    public string $outputUserId;

    #[\Override]
    public function persist(UserWithHashedPassword $user): string
    {
        $this->inputUser = $user;

        return $this->outputUserId;
    }
}
