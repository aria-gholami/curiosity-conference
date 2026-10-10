<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaAnswer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion as QuestionEntity;
use CuriosityConference\Application\Port\Outbound\TriviaQuestion\Persister as PersisterInterface;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaQuestion\Question;
use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * @throws PersistFailedException
     */
    #[\Override]
    public function persist(Question $question): QuestionEntity
    {
        try {
            $this->logger->info('Start persisting question.', [
                'questionTitle' => $question->title,
            ]);
            $questionEntity = new QuestionEntity(
                Uuid::v4()->toRfc4122(),
                $question->title,
                $question->quiz,
                new ArrayCollection(),
            );
            $questionEntity->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), $question->answers[0], 0 === $question->correctAnswerIndex));
            $questionEntity->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), $question->answers[1], 1 === $question->correctAnswerIndex));
            $questionEntity->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), $question->answers[2], 2 === $question->correctAnswerIndex));
            $questionEntity->addTriviaAnswer(new TriviaAnswer(Uuid::v4()->toRfc4122(), $question->answers[3], 3 === $question->correctAnswerIndex));
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
