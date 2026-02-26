<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Application\Port\Inbound\Quiz\DeleteHandler as DeleteHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Quiz\ByTitleDeleter;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Quiz\Title;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class DeleteHandler implements DeleteHandlerInterface
{
    public function __construct(
        private ByTitleDeleter $byTitleDeleter,
    ) {
    }

    /**
     * @throws DeleteFailedException
     */
    #[\Override]
    public function handle(Title $title): void
    {
        $this->byTitleDeleter->delete($title);
    }
}
