<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore;

/**
 * @template-implements \IteratorAggregate<int, TriviaHighscore>
 *
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class HighscoreCollection implements \IteratorAggregate
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
