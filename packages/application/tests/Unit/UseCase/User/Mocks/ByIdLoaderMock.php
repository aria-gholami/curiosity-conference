<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\User\ByIdLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;

final class ByIdLoaderMock implements ByIdLoader
{
    public string $inputId;
    public User $outputUser;
    public ?LoadEntityFailedException $throwException = null;

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(string $id): User
    {
        $this->inputId = $id;
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }

        return $this->outputUser;
    }
}
