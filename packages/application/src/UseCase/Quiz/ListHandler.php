<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Application\Port\Inbound\Quiz\ListHandler as ListHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Quiz\Loader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\QuizCollection;

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
