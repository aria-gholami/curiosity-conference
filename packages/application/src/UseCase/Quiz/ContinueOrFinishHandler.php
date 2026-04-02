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
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\Exception\CurrentQuestionNotFoundException;
use CuriosityConference\Domain\Quiz\Id;
use Psr\Log\LoggerInterface;

final readonly class ContinueOrFinishHandler implements ContinueOrFinishHandlerInterface
{
    public function __construct(
        private RoomByIdLoader $roomByIdLoader,
        private QuestionByIdLoader $questionByIdLoader,
        private RoomUpdater $roomUpdater,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws CurrentQuestionNotFoundException
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
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
    }
}
