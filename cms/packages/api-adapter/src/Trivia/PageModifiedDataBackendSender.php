<?php

declare(strict_types=1);

namespace CuriosityConference\ApiAdapter\Trivia;

use CuriosityConference\Core\Trivia\Exception\CouldNotSendTriviaModifiedDataException;
use CuriosityConference\Core\Trivia\PageModifiedDataBackendSender as PageModifiedDataBackendSenderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class PageModifiedDataBackendSender implements PageModifiedDataBackendSenderInterface
{
    public const string METHOD = 'POST';
    public const string URL = '/trivia/quizzes';

    public function __construct(
        private string $baseUrl,
        private HttpClientInterface $client,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws CouldNotSendTriviaModifiedDataException
     */
    #[\Override]
    public function post(array $postData): void
    {
        try {
            $this->logger->info('Start sending trivia modified data to backend.', [
                'postData' => $postData,
            ]);
            $response = $this->client->request(
                self::METHOD,
                $this->baseUrl . self::URL,
                [
                    'json' => $postData,
                ],
            );
            if (200 !== $response->getStatusCode()) {
                $this->logger->notice('Sending trivia modified data has no response code 200.', [
                    'postData' => $postData,
                    'statusCode' => $response->getStatusCode(),
                ]);
                throw CouldNotSendTriviaModifiedDataException::sendFailed('Response code is not 200.');
            }

            $this->logger->info('Trivia modified data is sent successfully.', [
                'postData' => $postData,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Failed to send trivia modified data.', [
                'postData' => $postData,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw CouldNotSendTriviaModifiedDataException::sendFailed($exception->getMessage());
        }
    }
}
