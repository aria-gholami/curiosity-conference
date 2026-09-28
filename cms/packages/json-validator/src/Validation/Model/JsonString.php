<?php

declare(strict_types=1);

namespace CuriosityConference\JsonValidator\Validation\Model;

use CuriosityConference\Core\Exception\EmptyStringException;

final readonly class JsonString
{
    /**
     * @throws EmptyStringException
     */
    public function __construct(
        public string $value,
    ) {
        if ('' === $this->value) {
            throw EmptyStringException::stringIsEmpty('jsonString');
        }
    }
}
