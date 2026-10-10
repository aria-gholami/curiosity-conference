<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuiz;

use CuriosityConference\Application\Port\Inbound\TriviaQuiz\ListHandler as ListHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\Loader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\QuizCollection;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class ListHandler implements ListHandlerInterface
{
    public function __construct(
        private Loader $loader,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function handle(): QuizCollection
    {
        return $this->loader->load();
    }
}
