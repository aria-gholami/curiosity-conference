<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks;

use CuriosityConference\Application\Port\Outbound\User\Persister;
use CuriosityConference\Domain\User\Model\UserName;

final class PersisterMock implements Persister
{
    public UserName $inputUserName;
    public string $outputUserId;

    #[\Override]
    public function persist(UserName $userName): string
    {
        $this->inputUserName = $userName;

        return $this->outputUserId;
    }
}
