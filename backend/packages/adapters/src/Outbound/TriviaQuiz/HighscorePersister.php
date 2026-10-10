<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\HighscorePersister as HighscorePersisterInterface;
use CuriosityConference\Domain\Exception\PersistFailedException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class HighscorePersister implements HighscorePersisterInterface
{
    public function __construct(
        private EntityManagerInterface $highscoreEntityManager,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws PersistFailedException
     */
    #[\Override]
    public function persist(TriviaQuizRoom $quizRoom, User $user): void
    {
        try {
            $this->logger->info('Start persisting new highscore.', [
                'quizRoomId' => $quizRoom->getId(),
            ]);
            $highscore = new TriviaHighscore(
                Uuid::v4()->toRfc4122(),
                $quizRoom,
                $user,
                0,
            );
            $this->highscoreEntityManager->persist($highscore);
            $this->highscoreEntityManager->flush();
            $this->logger->info('Persisting new highscore was successful.', [
                'quizRoomId' => $quizRoom->getId(),
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Persisting new highscore was not successful.', [
                'quizRoomId' => $quizRoom->getId(),
            ]);
            $this->logger->debug('Persisting new highscore was not successful.', [
                'quizRoomId' => $quizRoom->getId(),
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw PersistFailedException::persistFailed(TriviaHighscore::class);
        }
    }
}
