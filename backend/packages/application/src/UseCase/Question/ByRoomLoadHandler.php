<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Question;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
use CuriosityConference\Application\Port\Inbound\Question\ByRoomLoadHandler as ByRoomLoadHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByIdLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\Id;
use Psr\Log\LoggerInterface;

final readonly class ByRoomLoadHandler implements ByRoomLoadHandlerInterface
{
    public function __construct(
        private RoomByIdLoader $roomByIdLoader,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function handleAndGet(Id $id): Question
    {
        $currentQuestion = $this->roomByIdLoader->load($id->value)->getCurrentQuestion();
        if (!$currentQuestion instanceof Question) {
            $this->logger->notice('Quiz room has no current question.', [
                'quizRoomId' => $id->value,
            ]);
            throw LoadEntityFailedException::loadFailed(Question::class);
        }

        return $currentQuestion;
    }
}
