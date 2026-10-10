<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz as QuizEntity;

/**
 * @template-implements \IteratorAggregate<int, QuizEntity>
 *
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class QuizCollection implements \IteratorAggregate
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

    public function isEmpty(): bool
    {
        return 0 === \count($this->data);
    }
}
