<?php

declare(strict_types=1);

namespace CuriosityConference\ApiAdapter\Tests\Questionnaire;

use CuriosityConference\ApiAdapter\Questionnaire\PageModifiedDataBackendSender;
use CuriosityConference\ApiAdapter\Tests\Mocks\HttpClientMock;
use CuriosityConference\ApiAdapter\Tests\Mocks\LoggerMock;
use CuriosityConference\ApiAdapter\Tests\Mocks\ResponseMock;
use CuriosityConference\Core\Questionnaire\Exception\CouldNotSendQuestionnaireModifiedDataException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PageModifiedDataBackendSender::class)]
final class PageModifiedDataBackendSenderTest extends TestCase
{
    private string $baseUrl = 'test';
    private string $apiKey = 'test';
    private HttpClientMock $httpClientMock;
    private LoggerMock $loggerMock;
    private PageModifiedDataBackendSender $pageModifiedDataBackendSender;

    protected function setUp(): void
    {
        $this->httpClientMock = new HttpClientMock();
        $this->loggerMock = new LoggerMock();
        $this->pageModifiedDataBackendSender = new PageModifiedDataBackendSender($this->baseUrl, $this->apiKey, $this->httpClientMock, $this->loggerMock);
    }

    public function testPostIsValid(): void
    {
        $expectedBodyAsArray = [['testBody']];
        $responseMock = new ResponseMock();
        $responseMock->outputStatusCode = 201;
        $responseMock->outputContent = 'testBody';
        $responseMock->outputResponseAsArray = $expectedBodyAsArray;
        $this->httpClientMock->outputResponse = $responseMock;
        $requestBody = [
            'test' => 'test',
        ];
        $this->pageModifiedDataBackendSender->post($requestBody);

        self::assertSame(PageModifiedDataBackendSender::METHOD, $this->httpClientMock->inputMethod);
        self::assertSame($this->baseUrl . PageModifiedDataBackendSender::URL, $this->httpClientMock->inputUrl);
        self::assertEquals([
            'json' => $requestBody,
            'headers' => [
                'API-Key' => $this->apiKey,
            ],
        ], $this->httpClientMock->inputOptions);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start sending questionnaire modified data to backend.',
                    'context' => [
                        'postData' => $requestBody,
                    ],
                ],
                [
                    'message' => 'Questionnaire modified data is sent successfully.',
                    'context' => [
                        'postData' => $requestBody,
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testPostWith500ResponseCode(): void
    {
        $expectedBodyAsArray = [['testBody']];
        $responseMock = new ResponseMock();
        $responseMock->outputStatusCode = 500;
        $responseMock->outputContent = 'testBody';
        $responseMock->outputResponseAsArray = $expectedBodyAsArray;
        $this->httpClientMock->outputResponse = $responseMock;
        $requestBody = [
            'test' => 'test',
        ];
        try {
            $this->pageModifiedDataBackendSender->post($requestBody);
            $this->fail('CouldNotSendQuestionnaireModifiedDataException was expected to be thrown.');
        } catch (CouldNotSendQuestionnaireModifiedDataException $exception) {
            self::assertSame('Questionnaire modified data could not be sent: Questionnaire modified data could not be sent: Response code is not 201.', $exception->getMessage());
        }

        self::assertSame(PageModifiedDataBackendSender::METHOD, $this->httpClientMock->inputMethod);
        self::assertSame($this->baseUrl . PageModifiedDataBackendSender::URL, $this->httpClientMock->inputUrl);
        self::assertEquals([
            'json' => $requestBody,
            'headers' => [
                'API-Key' => $this->apiKey,
            ],
        ], $this->httpClientMock->inputOptions);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start sending questionnaire modified data to backend.',
                    'context' => [
                        'postData' => $requestBody,
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Sending questionnaire modified data has no response code 201.',
                    'context' => [
                        'postData' => $requestBody,
                        'statusCode' => 500,
                    ],
                ],
                [
                    'message' => 'Failed to send questionnaire modified data.',
                    'context' => [
                        'postData' => $requestBody,
                        'exceptionMessage' => 'Questionnaire modified data could not be sent: Response code is not 201.',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
