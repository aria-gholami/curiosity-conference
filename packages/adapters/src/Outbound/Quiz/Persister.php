<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz as QuizEntity;
use CuriosityConference\Application\Port\Outbound\Quiz\Persister as PersisterInterface;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\Quiz;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class Persister implements PersisterInterface
{
    public function __construct(
        private EntityManagerInterface $userEntityManager,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws PersistFailedException
     */
    #[\Override]
    public function persist(Quiz $quiz): string
    {
        try {
            $this->logger->info('Start persisting quiz.');
            $this->logger->debug('Start persisting quiz.', [
                'quizTitle' => $quiz->title,
                'quizDescription' => $quiz->description,
                'quizCategory' => $quiz->category,
            ]);
            $quizEntity = new QuizEntity(
                Uuid::v4()->toRfc4122(),
                $quiz->title,
                $quiz->description,
                $quiz->category,
            );
            $this->userEntityManager->persist($quizEntity);
            $this->userEntityManager->flush();

            $this->logger->info('Persisting quiz was successful.');
            $this->logger->debug('Persisting quiz was successful.', [
                'quizTitle' => $quiz->title,
                'quizDescription' => $quiz->description,
                'quizCategory' => $quiz->category,
            ]);

            return $quizEntity->getId();
        } catch (\Throwable $exception) {
            $this->logger->notice('Persisting quiz was not successful.');
            $this->logger->debug('Persisting quiz was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
                'quizTitle' => $quiz->title,
                'quizDescription' => $quiz->description,
                'quizCategory' => $quiz->category,
            ]);

            throw PersistFailedException::persistFailed(QuizEntity::class);
        }
    }
}
