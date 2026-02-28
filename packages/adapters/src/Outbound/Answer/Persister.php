<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Answer;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Answer as AnswerEntity;
use CuriosityConference\Application\Port\Outbound\Answer\Persister as PersisterInterface;
use CuriosityConference\Domain\Answer\Answer;
use CuriosityConference\Domain\Exception\PersistFailedException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class Persister implements PersisterInterface
{
    public function __construct(
        private EntityManagerInterface $answerEntityManager,
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function persist(Answer $answer): string
    {
        try {
            $this->logger->info('Start persisting answers.', [
                'questionTitle' => $answer->question->getTitle(),
            ]);
            $answerEntity = new AnswerEntity(
                Uuid::v4()->toRfc4122(),
                $answer->question,
                $answer->answers[0],
                0 === $answer->correctAnswerIndex,
            );
            $this->answerEntityManager->persist($answerEntity);
            $answerEntity = new AnswerEntity(
                Uuid::v4()->toRfc4122(),
                $answer->question,
                $answer->answers[1],
                1 === $answer->correctAnswerIndex,
            );
            $this->answerEntityManager->persist($answerEntity);
            $answerEntity = new AnswerEntity(
                Uuid::v4()->toRfc4122(),
                $answer->question,
                $answer->answers[2],
                2 === $answer->correctAnswerIndex,
            );
            $this->answerEntityManager->persist($answerEntity);
            $answerEntity = new AnswerEntity(
                Uuid::v4()->toRfc4122(),
                $answer->question,
                $answer->answers[3],
                3 === $answer->correctAnswerIndex,
            );
            $this->answerEntityManager->persist($answerEntity);
            $this->answerEntityManager->flush();

            $this->logger->info('Persisting answers was successful.', [
                'questionTitle' => $answer->question->getTitle(),
            ]);

            return $answerEntity->getId();
        } catch (\Throwable $exception) {
            $this->logger->notice('Persisting answers was not successful.', [
                'questionTitle' => $answer->question->getTitle(),
            ]);
            $this->logger->debug('Persisting answers was not successful.', [
                'questionTitle' => $answer->question->getTitle(),
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw PersistFailedException::persistFailed(AnswerEntity::class);
        }
    }
}
