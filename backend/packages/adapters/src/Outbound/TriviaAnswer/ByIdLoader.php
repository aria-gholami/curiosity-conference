<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaAnswer;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaAnswer;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaAnswerRepository;
use CuriosityConference\Application\Port\Outbound\TriviaAnswer\ByIdLoader as ByIdLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Psr\Log\LoggerInterface;

final readonly class ByIdLoader implements ByIdLoaderInterface
{
    public function __construct(
        private TriviaAnswerRepository $answerRepository,
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function load(string $id): TriviaAnswer
    {
        try {
            $this->logger->info('Start loading answer by answer id.', [
                'answerId' => $id,
            ]);
            $answer = $this->answerRepository->findOneBy(['id' => $id]);
            if (!$answer instanceof TriviaAnswer) {
                throw LoadEntityFailedException::loadFailed(TriviaAnswer::class);
            }
            $this->logger->info('Loading answer by answer id was successful.', [
                'answerId' => $id,
            ]);

            return $answer;
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading answer by answer id was not successful.', [
                'answerId' => $id,
            ]);
            $this->logger->debug('Loading answer by answer id was not successful.', [
                'answerId' => $id,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(TriviaAnswer::class);
        }
    }
}
