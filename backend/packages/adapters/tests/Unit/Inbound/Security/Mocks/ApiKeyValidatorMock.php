<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Security\Mocks;

use CuriosityConference\Application\Port\Inbound\Security\ApiKeyValidator;
use CuriosityConference\Domain\Exception\InvalidApiKeyException;
use Symfony\Component\HttpFoundation\Request;

final class ApiKeyValidatorMock implements ApiKeyValidator
{
    public ?Request $inputRequest = null;
    public ?InvalidApiKeyException $thrownException = null;

    /**
     * @throws InvalidApiKeyException
     */
    #[\Override]
    public function validateRequest(Request $request): void
    {
        $this->inputRequest = $request;
        if ($this->thrownException instanceof InvalidApiKeyException) {
            throw $this->thrownException;
        }
    }
}
