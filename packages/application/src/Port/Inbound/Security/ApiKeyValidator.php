<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Security;

use CuriosityConference\Domain\Exception\InvalidApiKeyException;
use Symfony\Component\HttpFoundation\Request;

interface ApiKeyValidator
{
    /**
     * @throws InvalidApiKeyException
     */
    public function validateRequest(Request $request): void;
}
