<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Mocks;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\TokenFactoryInterface;
use Symfony\Component\Mercure\Update;

final class HubMock implements HubInterface
{
    public string $outputPublicUrl;
    public ?TokenFactoryInterface $outputTokenFactory;
    public Update $inputUpdate;
    public string $outputUpdate;

    public function getPublicUrl(): string
    {
        return $this->outputPublicUrl;
    }

    public function getFactory(): ?TokenFactoryInterface
    {
        return $this->outputTokenFactory;
    }

    public function publish(Update $update): string
    {
        $this->inputUpdate = $update;

        return $this->outputUpdate;
    }
}
