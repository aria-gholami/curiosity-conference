<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuiz;

use CuriosityConference\Application\Port\Inbound\TriviaQuiz\HighscoreUpdateHandler as HighscoreUpdateHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaAnswer\ByIdLoader as AnswerByIdLoader;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\HighscoreLoader;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\HighscoreUpdater;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomByIdLoader;
use CuriosityConference\Application\Port\Outbound\User\ByIdLoader as UserByIdLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\TriviaQuiz\HighscoreUpdateRequestBody;
use CuriosityConference\Domain\TriviaQuiz\HighscoreUpdateResponse;

final readonly class HighscoreUpdateHandler implements HighscoreUpdateHandlerInterface
{
    public function __construct(
        private AnswerByIdLoader $answerByIdLoader,
        private UserByIdLoader $userByIdLoader,
        private RoomByIdLoader $roomByIdLoader,
        private HighscoreLoader $highscoreLoader,
        private HighscoreUpdater $highscoreUpdater,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     * @throws UpdateFailedException
     */
    #[\Override]
    public function handleAndGet(HighscoreUpdateRequestBody $highscoreUpdateRequestBody): HighscoreUpdateResponse
    {
        $answer = $this->answerByIdLoader->load($highscoreUpdateRequestBody->answerId);
        $user = $this->userByIdLoader->load($highscoreUpdateRequestBody->userId);
        $quizRoom = $this->roomByIdLoader->load($highscoreUpdateRequestBody->quizRoomId);
        $highscore = $this->highscoreLoader->load($user, $quizRoom);
        if (!$answer->isCorrect()) {
            return new HighscoreUpdateResponse($highscore, $answer);
        }

        $highscore->addScore(1);
        $this->highscoreUpdater->update($highscore);

        return new HighscoreUpdateResponse($highscore, $answer);
    }
}
