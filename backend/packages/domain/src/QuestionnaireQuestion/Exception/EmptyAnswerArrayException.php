<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\QuestionnaireQuestion\Exception;

use CuriosityConference\Domain\Exception\ValidationException;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class EmptyAnswerArrayException extends ValidationException
{
    public static function isInvalid(string $identifier): self
    {
        return new self(
            parent::buildMessage(
                $identifier,
                \sprintf('Value for "%s" should not be empty.', $identifier),
            ),
        );
    }
}
