<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Application\Port\Inbound\Quiz\ContinueOrFinishHandler as HighscoreUpdateHandlerInterface;
use CuriosityConference\Application\Port\Inbound\Quiz\NextQuestionPersistHandler;
use CuriosityConference\Application\Port\Outbound\Answer\ByIdLoader as AnswerByIdLoader;
use CuriosityConference\Application\Port\Outbound\Quiz\HighscoreLoader;
use CuriosityConference\Application\Port\Outbound\Quiz\HighscoreUpdater;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByIdLoader;
use CuriosityConference\Application\Port\Outbound\User\ByIdLoader as UserByIdLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\ContinueOrFinishRequestBody;
use CuriosityConference\Domain\Quiz\ContinueOrFinishResponse;
use CuriosityConference\Domain\Quiz\Exception\CurrentQuestionNotFoundException;

final readonly class ContinueOrFinishHandler implements HighscoreUpdateHandlerInterface
{
    public function __construct(
        private NextQuestionPersistHandler $nextQuestionPersistHandler,
        private AnswerByIdLoader $answerByIdLoader,
        private UserByIdLoader $userByIdLoader,
        private RoomByIdLoader $roomByIdLoader,
        private HighscoreLoader $highscoreLoader,
        private HighscoreUpdater $highscoreUpdater,
    ) {
    }

    /**
     * @throws CurrentQuestionNotFoundException
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     * @throws UpdateFailedException
     */
    #[\Override]
    public function handleAndGet(ContinueOrFinishRequestBody $continueOrFinishRequestBody): ContinueOrFinishResponse
    {
        $this->nextQuestionPersistHandler->handle($continueOrFinishRequestBody->quizRoomId);
        $answer = $this->answerByIdLoader->load($continueOrFinishRequestBody->answerId);
        $user = $this->userByIdLoader->load($continueOrFinishRequestBody->userId);
        $quizRoom = $this->roomByIdLoader->load($continueOrFinishRequestBody->quizRoomId);
        $highscore = $this->highscoreLoader->load($user, $quizRoom);
        if (!$answer->isCorrect()) {
            return new ContinueOrFinishResponse($highscore, $answer);
        }

        $highscore->addScore(1);
        $this->highscoreUpdater->update($highscore);

        return new ContinueOrFinishResponse($highscore, $answer);
    }
}
