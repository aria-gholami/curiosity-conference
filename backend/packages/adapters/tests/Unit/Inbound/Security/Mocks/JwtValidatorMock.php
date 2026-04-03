<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Security\Mocks;

use CuriosityConference\Application\Port\Inbound\Security\JwtValidator;
use CuriosityConference\Domain\Exception\InvalidJwtException;
use Symfony\Component\HttpFoundation\Request;

final class JwtValidatorMock implements JwtValidator
{
    public Request $inputRequest;
    public ?InvalidJwtException $throwException = null;

    /**
     * @throws InvalidJwtException
     */
    #[\Override]
    public function validateRequest(Request $request): void
    {
        $this->inputRequest = $request;
        if ($this->throwException instanceof InvalidJwtException) {
            throw $this->throwException;
        }
    }
}
