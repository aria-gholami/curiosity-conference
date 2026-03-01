<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Question;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question as QuestionEntity;

/**
 * @template-implements \IteratorAggregate<int, QuestionEntity>
 *
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class QuestionCollection implements \IteratorAggregate
{
    public function __construct(public array $data)
    {
    }

    #[\Override]
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->data);
    }

    public function contains(int $index): bool
    {
        return \array_key_exists($index, $this->data);
    }
}
