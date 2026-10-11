<?php

declare(strict_types=1);

namespace CuriosityConference\ApiAdapter\Questionnaire;

use CuriosityConference\Core\Questionnaire\Exception\CouldNotSendQuestionnaireModifiedDataException;
use CuriosityConference\Core\Questionnaire\PageModifiedDataBackendSender as PageModifiedDataBackendSenderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class PageModifiedDataBackendSender implements PageModifiedDataBackendSenderInterface
{
    public const string METHOD = 'POST';
    public const string URL = '/questionnaire/questions';

    public function __construct(
        private string $baseUrl,
        private string $apiKey,
        private HttpClientInterface $client,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws CouldNotSendQuestionnaireModifiedDataException
     */
    #[\Override]
    public function post(array $postData): void
    {
        try {
            $this->logger->info('Start sending questionnaire modified data to backend.', [
                'postData' => $postData,
            ]);
            $response = $this->client->request(
                self::METHOD,
                $this->baseUrl . self::URL,
                [
                    'headers' => [
                        'API-Key' => $this->apiKey,
                    ],
                    'json' => $postData,
                ],
            );
            if (201 !== $response->getStatusCode()) {
                $this->logger->notice('Sending questionnaire modified data has no response code 201.', [
                    'postData' => $postData,
                    'statusCode' => $response->getStatusCode(),
                ]);
                throw CouldNotSendQuestionnaireModifiedDataException::sendFailed('Response code is not 201.');
            }

            $this->logger->info('Questionnaire modified data is sent successfully.', [
                'postData' => $postData,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Failed to send questionnaire modified data.', [
                'postData' => $postData,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw CouldNotSendQuestionnaireModifiedDataException::sendFailed($exception->getMessage());
        }
    }
}
