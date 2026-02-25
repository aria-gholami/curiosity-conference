<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\User\ByEmailLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;

final class ByEmailLoaderMock implements ByEmailLoader
{
    public string $inputEmail;
    public User $outputUser;
    public ?LoadEntityFailedException $throwException = null;

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(string $email): User
    {
        $this->inputEmail = $email;
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }

        return $this->outputUser;
    }
}
