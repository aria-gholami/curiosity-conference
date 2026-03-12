<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Security;

use CuriosityConference\Domain\Exception\InvalidJwtException;
use Symfony\Component\HttpFoundation\Request;

interface JwtValidator
{
    /**
     * @throws InvalidJwtException
     */
    public function validateRequest(Request $request): void;
}
