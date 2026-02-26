<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Application\Port\Inbound\Quiz\CreateHandler as CreateHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Quiz\Persister;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\Quiz;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class CreateHandler implements CreateHandlerInterface
{
    public function __construct(
        private Persister $persister,
    ) {
    }

    /**
     * @throws PersistFailedException
     */
    #[\Override]
    public function handle(Quiz $quiz): void
    {
        $this->persister->persist($quiz);
    }
}
