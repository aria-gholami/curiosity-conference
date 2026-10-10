<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuiz;

use CuriosityConference\Application\Port\Inbound\TriviaQuiz\CreateHandler as CreateHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\Persister;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaQuiz\Quiz;

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
    public function handle(Quiz $quiz): string
    {
        return $this->persister->persist($quiz);
    }
}
