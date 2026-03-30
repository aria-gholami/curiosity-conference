<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Application\Port\Inbound\Quiz\StartHandler as StartHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Quiz\QuizRoomUpdater;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Id;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class StartHandler implements StartHandlerInterface
{
    public function __construct(
        private QuizRoomUpdater $quizRoomUpdater,
    ) {
    }

    /**
     * @throws UpdateFailedException
     */
    #[\Override]
    public function Handle(Id $quizRoomId): void
    {
        $this->quizRoomUpdater->updateStatus($quizRoomId, QuizRoom::STATUS_STARTED);
    }
}
