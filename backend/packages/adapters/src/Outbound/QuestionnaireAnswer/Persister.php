<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\QuestionnaireAnswer;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireAnswer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireQuestion;
use CuriosityConference\Application\Port\Outbound\QuestionnaireAnswer\Persister as PersisterInterface;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\QuestionnaireQuestion\Question;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class Persister implements PersisterInterface
{
    public function __construct(
        private EntityManagerInterface $questionnaireAnswerEntityManager,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws PersistFailedException
     */
    #[\Override]
    public function persist(Question $question): void
    {
        try {
            $this->logger->info('Start persisting questionnaire question.', [
                'questionnaireQuestionTitle' => $question->title,
            ]);
            $questionnaireQuestionEntity = new QuestionnaireQuestion(
                Uuid::v4()->toRfc4122(),
                $question->title,
            );
            $this->questionnaireAnswerEntityManager->persist(
                $questionnaireQuestionEntity,
            );
            foreach ($question->answers as $answer) {
                $questionnaireAnswerEntity = new QuestionnaireAnswer(
                    Uuid::v4()->toRfc4122(),
                    $answer,
                    $questionnaireQuestionEntity,
                );
                $this->questionnaireAnswerEntityManager->persist($questionnaireAnswerEntity);
            }

            $this->questionnaireAnswerEntityManager->flush();
            $this->logger->info('Persisting questionnaire question was successful.', [
                'questionnaireQuestionTitle' => $question->title,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Persisting questionnaire question was not successful.', [
                'questionnaireQuestionTitle' => $question->title,
            ]);
            $this->logger->debug('Persisting questionnaire question was not successful.', [
                'questionnaireQuestionTitle' => $question->title,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw PersistFailedException::persistFailed(QuestionnaireAnswer::class);
        }
    }
}
