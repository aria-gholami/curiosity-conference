<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\QuestionnaireSettings;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireSettings;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuestionnaireSettingsRepository;
use CuriosityConference\Application\Port\Outbound\QuestionnaireSettings\Upserter as UpserterInterface;
use CuriosityConference\Domain\Exception\UpsertFailedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class Upserter implements UpserterInterface
{
    public function __construct(
        private QuestionnaireSettingsRepository $questionnaireSettingsRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws UpsertFailedException
     */
    #[\Override]
    public function upsert(bool $showResults): void
    {
        try {
            $this->logger->info('Start upserting questionnaire settings.', [
                'showResults' => $showResults,
            ]);

            $questionnaireSettingsGroup = $this->questionnaireSettingsRepository->findAll();
            if (0 === \count($questionnaireSettingsGroup)) {
                $questionnaireSettings = new QuestionnaireSettings(
                    Uuid::v4()->toRfc4122(),
                    $showResults,
                );
                $this->questionnaireSettingsRepository->persist($questionnaireSettings);
            }
            if (\count($questionnaireSettingsGroup) > 0) {
                /** @var QuestionnaireSettings $questionnaireSettings */
                $questionnaireSettings = $questionnaireSettingsGroup[0];
                $questionnaireSettings->setIsShownResults($showResults);
                $this->questionnaireSettingsRepository->persist($questionnaireSettings);
            }

            $this->logger->info('Upserting questionnaire settings was successful.', [
                'showResults' => $showResults,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Upserting questionnaire settings was not successful.', [
                'showResults' => $showResults,
            ]);
            $this->logger->debug('Upserting questionnaire settings was not successful.', [
                'showResults' => $showResults,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw UpsertFailedException::upsertFailed(QuestionnaireSettings::class);
        }
    }
}
