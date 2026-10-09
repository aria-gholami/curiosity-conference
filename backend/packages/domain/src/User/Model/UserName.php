<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\User\Model;

use CuriosityConference\Domain\User\Exception\InvalidNameException;

final readonly class UserName
{
    /**
     * @throws InvalidNameException
     */
    public function __construct(
        public string $value,
    ) {
        if ('' === $this->value || \mb_strlen($this->value) > 15) {
            throw InvalidNameException::isInvalid('userName');
        }
    }
}
