<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Question;

use CuriosityConference\Application\Port\Inbound\Question\DeleteHandler as DeleteHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Question\ByIdDeleter;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Quiz\Id;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class DeleteHandler implements DeleteHandlerInterface
{
    public function __construct(
        private ByIdDeleter $byIdDeleter,
    ) {
    }

    /**
     * @throws DeleteFailedException
     */
    #[\Override]
    public function handle(Id $id): void
    {
        $this->byIdDeleter->delete($id);
    }
}
