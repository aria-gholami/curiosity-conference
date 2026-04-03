<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\HighscoreRepository;
use CuriosityConference\Application\Port\Outbound\Quiz\HighscoreLoader as HighscoreLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Psr\Log\LoggerInterface;

final readonly class HighscoreLoader implements HighscoreLoaderInterface
{
    public function __construct(
        private HighscoreRepository $highscoreRepository,
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function load(User $user, QuizRoom $quizRoom): Highscore
    {
        try {
            $this->logger->info('Start loading highscore.', [
                'quizRoomId' => $quizRoom->getId(),
            ]);
            $highscore = $this->highscoreRepository->findOneBy(['user' => $user, 'quizRoom' => $quizRoom]);
            if (!$highscore instanceof Highscore) {
                throw LoadEntityFailedException::loadFailed(Highscore::class);
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

            throw LoadEntityFailedException::loadFailed(Highscore::class);
        }
    }
}
