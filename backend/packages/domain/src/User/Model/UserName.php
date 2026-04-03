<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;

final readonly class UserName
{
    /**
     * @throws EmptyStringException
     */
    public function __construct(
        public string $value,
    ) {
        if ('' === $this->value) {
            throw EmptyStringException::stringIsEmpty('userName');
        }
    }
}
