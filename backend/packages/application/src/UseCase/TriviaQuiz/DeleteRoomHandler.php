<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuiz;

use CuriosityConference\Application\Port\Inbound\TriviaQuiz\DeleteRoomHandler as DeleteRoomHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomByIdDeleter;
use CuriosityConference\Domain\TriviaQuiz\Id;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class DeleteRoomHandler implements DeleteRoomHandlerInterface
{
    public function __construct(
        private RoomByIdDeleter $roomByIdDeleter,
        private HubInterface $hub,
    ) {
    }

    #[\Override]
    public function handle(Id $quizRoomId): void
    {
        $this->roomByIdDeleter->delete($quizRoomId);
        $this->hub->publish(new Update(
            'trivia/quiz/room/list',
            \json_encode(['action' => 'remove', 'quizRoomResults' => [$quizRoomId->value]], \JSON_THROW_ON_ERROR),
        ));
        $this->hub->publish(new Update(
            \sprintf('trivia/quiz/room/%s/participants', $quizRoomId->value),
            \json_encode(['action' => 'removeQuizRoom', 'quizRoomId' => $quizRoomId->value], \JSON_THROW_ON_ERROR),
        ));
    }
}
