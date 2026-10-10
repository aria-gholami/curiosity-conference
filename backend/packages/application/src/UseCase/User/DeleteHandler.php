<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Application\Port\Inbound\User\DeleteHandler as DeleteHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomResultsByUserIdLoader;
use CuriosityConference\Application\Port\Outbound\User\ByIdDeleter;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Model\Id;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final readonly class DeleteHandler implements DeleteHandlerInterface
{
    public function __construct(
        private RoomResultsByUserIdLoader $roomByUserIdLoader,
        private ByIdDeleter $byIdDeleter,
        private HubInterface $hub,
    ) {
    }

    /**
     * @throws DeleteFailedException
     * @throws LoadEntityFailedException
     * @throws \JsonException
     */
    #[\Override]
    public function handle(Id $userId): void
    {
        $quizRoomResults = $this->roomByUserIdLoader->load($userId->value);
        $this->byIdDeleter->delete($userId);
        if (0 === \count($quizRoomResults)) {
            return;
        }

        $quizRoomResultWithStatusNotClosed = \array_values(
            \array_filter(
                $quizRoomResults,
                static fn (array $quizRoomResult): bool => TriviaQuizRoom::STATUS_CLOSED !== $quizRoomResult['status'],
            ),
        );
        $this->hub->publish(new Update(
            'trivia/quiz/room/list',
            \json_encode(['action' => 'remove', 'quizRoomResults' => \array_column($quizRoomResults, 'id')], \JSON_THROW_ON_ERROR),
        ));
        if (\count($quizRoomResultWithStatusNotClosed) > 0) {
            $this->hub->publish(
                new Update(
                    \sprintf('trivia/quiz/room/%s/participants', $quizRoomResultWithStatusNotClosed[0]['id']),
                    \json_encode(['action' => 'remove', 'participantId' => $userId->value], \JSON_THROW_ON_ERROR),
                ),
            );
        }
    }
}
