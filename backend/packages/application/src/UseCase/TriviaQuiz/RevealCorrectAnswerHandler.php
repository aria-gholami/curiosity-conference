<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuiz;

use CuriosityConference\Application\Port\Inbound\TriviaQuiz\RevealCorrectAnswerHandler as RevealCorrectAnswerHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomByIdLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class RevealCorrectAnswerHandler implements RevealCorrectAnswerHandlerInterface
{
    public function __construct(
        private RoomByIdLoader $roomByIdLoader,
        private HubInterface $hub,
    ) {
    }

    /**
     * @throws \JsonException
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function handle(Id $quizRoomId): void
    {
        $quizRoom = $this->roomByIdLoader->load($quizRoomId->value);
        $update = new Update(
            \sprintf('trivia/quiz/room/%s/answer/reveal', $quizRoomId->value),
            \json_encode(['action' => 'reveal', 'hostId' => $quizRoom->getHost()->getId()], \JSON_THROW_ON_ERROR),
        );
        $this->hub->publish($update);
    }
}
