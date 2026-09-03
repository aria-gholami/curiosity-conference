<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Quiz;

use IteratorAggregate;

/**
 * @template-implements IteratorAggregate<int, QuizRoomHighscore>
 *
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class AllRoomsHighscoreCollection implements \IteratorAggregate
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
