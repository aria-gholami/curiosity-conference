<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaHighscoreRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\HighscoreLoader as HighscoreLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Psr\Log\LoggerInterface;

final readonly class HighscoreLoader implements HighscoreLoaderInterface
{
    public function __construct(
        private TriviaHighscoreRepository $highscoreRepository,
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function load(User $user, TriviaQuizRoom $quizRoom): TriviaHighscore
    {
        try {
            $this->logger->info('Start loading highscore.', [
                'quizRoomId' => $quizRoom->getId(),
            ]);
            $highscore = $this->highscoreRepository->findOneBy(['user' => $user, 'triviaQuizRoom' => $quizRoom]);
            if (!$highscore instanceof TriviaHighscore) {
                throw LoadEntityFailedException::loadFailed(TriviaHighscore::class);
            }
            $this->logger->info('Loading highscore was successful.', [
                'quizRoomId' => $quizRoom->getId(),
            ]);

            return $highscore;
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading highscore was not successful.', [
                'quizRoomId' => $quizRoom->getId(),
            ]);
            $this->logger->debug('Loading highscore was not successful.', [
                'quizRoomId' => $quizRoom->getId(),
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(TriviaHighscore::class);
        }
    }
}
