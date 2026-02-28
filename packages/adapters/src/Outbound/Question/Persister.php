<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Question;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question as QuestionEntity;
use CuriosityConference\Application\Port\Outbound\Question\Persister as PersisterInterface;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Question\Question;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class Persister implements PersisterInterface
{
    public function __construct(
        private EntityManagerInterface $questionEntityManager,
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function persist(Question $question): QuestionEntity
    {
        try {
            $this->logger->info('Start persisting question.', [
                'questionTitle' => $question->title,
            ]);
            $questionEntity = new QuestionEntity(
                Uuid::v4()->toRfc4122(),
                $question->quiz,
                $question->title,
            );
            $this->questionEntityManager->persist($questionEntity);
            $this->questionEntityManager->flush();

            $this->logger->info('Persisting question was successful.', [
                'questionTitle' => $question->title,
            ]);

            return $questionEntity;
        } catch (\Throwable $exception) {
            $this->logger->notice('Persisting question was not successful.', [
                'questionTitle' => $question->title,
            ]);
            $this->logger->debug('Persisting question was not successful.', [
                'questionTitle' => $question->title,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw PersistFailedException::persistFailed(QuestionEntity::class);
        }
    }
}
