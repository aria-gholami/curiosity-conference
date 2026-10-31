<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\QuestionnaireQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireAnswer;
use IteratorAggregate;

/**
 * @template-implements IteratorAggregate<int, QuestionnaireAnswer>
 *
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class AnswerCollection implements \IteratorAggregate
{
    public function __construct(public array $data)
    {
    }

    #[\Override]
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->data);
    }
}
