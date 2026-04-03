<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\User\ByUserNameLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;

final class ByUserNameLoaderMock implements ByUserNameLoader
{
    public string $inputUserName = '';
    public User $outputUser;
    public ?LoadEntityFailedException $throwException = null;

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(string $userName): User
    {
        $this->inputUserName = $userName;
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }

        return $this->outputUser;
    }
}
