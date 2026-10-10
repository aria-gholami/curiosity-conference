<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuestion;

use CuriosityConference\Application\Port\Inbound\TriviaQuestion\DeleteHandler as DeleteHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuestion\ByIdDeleter;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;

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
