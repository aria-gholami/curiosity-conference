<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;

/**
 * @template-implements \IteratorAggregate<int, Highscore>
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
