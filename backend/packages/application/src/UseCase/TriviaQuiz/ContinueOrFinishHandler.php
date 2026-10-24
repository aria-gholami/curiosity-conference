<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Application\Port\Inbound\TriviaQuiz\ContinueOrFinishHandler as ContinueOrFinishHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuestion\Loader as QuestionByIdLoader;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomByIdLoader;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomUpdater;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\TriviaQuiz\Exception\CurrentQuestionNotFoundException;
use CuriosityConference\Domain\TriviaQuiz\Id;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final readonly class ContinueOrFinishHandler implements ContinueOrFinishHandlerInterface
{
    public function __construct(
        private RoomByIdLoader $roomByIdLoader,
        private QuestionByIdLoader $questionByIdLoader,
        private RoomUpdater $roomUpdater,
        private HubInterface $hub,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws CurrentQuestionNotFoundException
     * @throws LoadEntityFailedException
     * @throws UpdateFailedException
     * @throws \JsonException
     */
    #[\Override]
    public function handle(Id $quizRoomId): void
    {
        $quizRoom = $this->roomByIdLoader->load($quizRoomId->value);
        $currentQuestion = $quizRoom->getCurrentTriviaQuestion() ?? null;
        if (!$currentQuestion instanceof TriviaQuestion) {
            $this->logger->notice('Current question is not found for quiz room.', [
                'quizRoomId' => $quizRoomId->value,
            ]);
            throw CurrentQuestionNotFoundException::notFound();
        }

        $questionCollection = $this->questionByIdLoader->load();
        $questionCounter = 0;
        foreach ($questionCollection as $question) {
            if ($question->getId() === $currentQuestion->getId()) {
                ++$questionCounter;
                break;
            }
            ++$questionCounter;
        }

        $quizRoom->setCurrentTriviaQuestion($questionCollection->data[$questionCounter] ?? null);
        $nextStatus = TriviaQuizRoom::STATUS_STARTED;
        if ($questionCounter >= \count($questionCollection->data)) {
            $nextStatus = TriviaQuizRoom::STATUS_FINISHED;
        }
        $quizRoom->setStatus($nextStatus);
        $this->roomUpdater->update($quizRoom);
        $this->hub->publish(new Update(
            \sprintf('trivia/quiz/room/%s/continue', $quizRoomId->value),
            \json_encode(['status' => $nextStatus, 'hostId' => $quizRoom->getHost()->getId()], \JSON_THROW_ON_ERROR),
        ));
        $this->hub->publish(new Update(
            'trivia/quiz/room/list',
            \json_encode(['action' => 'status', 'status' => $nextStatus, 'quizRoomId' => $quizRoomId->value], \JSON_THROW_ON_ERROR),
        ));
    }
}
