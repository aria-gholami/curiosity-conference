<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\Mocks;

use CuriosityConference\Application\Port\Outbound\Security\Argon2idHasher;

final class Argon2idHasherMock implements Argon2idHasher
{
    public string $inputValue;
    public string $outputValue;
    public string $inputPlainPassword;
    public string $inputHash;
    public bool $outputVerified;

    #[\Override]
    public function hash(string $value): string
    {
        $this->inputValue = $value;

        return $this->outputValue;
    }

    #[\Override]
    public function verify(string $plainPassword, string $hash): bool
    {
        $this->inputPlainPassword = $plainPassword;
        $this->inputHash = $hash;

        return $this->outputVerified;
    }
}
