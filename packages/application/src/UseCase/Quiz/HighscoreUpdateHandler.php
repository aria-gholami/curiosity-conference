<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Application\Port\Inbound\Quiz\HighscoreUpdateHandler as HighscoreUpdateHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Answer\ByIdLoader;
use CuriosityConference\Application\Port\Outbound\Quiz\HighscoreLoader;
use CuriosityConference\Application\Port\Outbound\Quiz\HighscoreUpdater;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\HighscoreUpdateRequestBody;
use CuriosityConference\Domain\Quiz\HighscoreUpdateResponse;

final readonly class HighscoreUpdateHandler implements HighscoreUpdateHandlerInterface
{
    public function __construct(
        private ByIdLoader $byIdLoader,
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
        $answer = $this->byIdLoader->load($highscoreUpdateRequestBody->answerId);
        $highscore = $this->highscoreLoader->load($highscoreUpdateRequestBody->userId, $highscoreUpdateRequestBody->quizRoomId);
        if (!$answer->isCorrect()) {
            return new HighscoreUpdateResponse($highscore, $answer);
        }

        $highscore->addScore(1);
        $this->highscoreUpdater->update($highscore);

        return new HighscoreUpdateResponse($highscore, $answer);
    }
}
