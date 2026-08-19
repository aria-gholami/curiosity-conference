<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Application\Port\Inbound\Quiz\ContinueOrFinishHandler as ContinueOrFinishHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Question\ByIdLoader as QuestionByIdLoader;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByIdLoader;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomUpdater;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Exception\CurrentQuestionNotFoundException;
use CuriosityConference\Domain\Quiz\Id;
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
        $currentQuestion = $quizRoom->getCurrentQuestion() ?? null;
        if (!$currentQuestion instanceof Question) {
            $this->logger->notice('Current question is not found for quiz room.', [
                'quizRoomId' => $quizRoomId->value,
            ]);
            throw CurrentQuestionNotFoundException::notFound();
        }

        $questionCollection = $this->questionByIdLoader->load($quizRoom->getQuiz());
        $questionCounter = 0;
        foreach ($questionCollection as $question) {
            if ($question->getId() === $currentQuestion->getId()) {
                ++$questionCounter;
                break;
            }
            ++$questionCounter;
        }

        $quizRoom->setCurrentQuestion($questionCollection->data[$questionCounter] ?? null);
        $nextStatus = QuizRoom::STATUS_STARTED;
        if ($questionCounter >= \count($questionCollection->data)) {
            $nextStatus = QuizRoom::STATUS_FINISHED;
        }
        $quizRoom->setStatus($nextStatus);
        $this->roomUpdater->update($quizRoom);
        $this->hub->publish(new Update(
            \sprintf('quiz/room/%s/continue', $quizRoomId->value),
            \json_encode(['status' => $nextStatus, 'hostId' => $quizRoom->getHost()->getId()], \JSON_THROW_ON_ERROR),
        ));
    }
}
