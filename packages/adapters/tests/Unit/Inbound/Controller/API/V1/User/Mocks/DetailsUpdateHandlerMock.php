<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks;

use CuriosityConference\Application\Port\Inbound\User\DetailsUpdateHandler;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\UserDetails;

final class DetailsUpdateHandlerMock implements DetailsUpdateHandler
{
    public UserDetails $inputUserDetails;
    public ?UpdateFailedException $throwException = null;

    /**
     * @throws UpdateFailedException
     */
    #[\Override]
    public function handle(UserDetails $userDetails): void
    {
        $this->inputUserDetails = $userDetails;
        if ($this->throwException instanceof UpdateFailedException) {
            throw $this->throwException;
        }
    }
}
