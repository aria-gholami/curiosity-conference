<?php

declare(strict_types=1);

namespace CuriosityConference\ApiAdapter\Tests\Trivia;

use CuriosityConference\ApiAdapter\Tests\Mocks\HttpClientMock;
use CuriosityConference\ApiAdapter\Tests\Mocks\LoggerMock;
use CuriosityConference\ApiAdapter\Tests\Mocks\ResponseMock;
use CuriosityConference\ApiAdapter\Trivia\PageModifiedDataBackendSender;
use CuriosityConference\Core\Trivia\Exception\CouldNotSendTriviaModifiedDataException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PageModifiedDataBackendSender::class)]
final class PageModifiedDataBackendSenderTest extends TestCase
{
    private string $baseUrl = 'test';
    private HttpClientMock $httpClientMock;
    private LoggerMock $loggerMock;
    private PageModifiedDataBackendSender $pageModifiedDataBackendSender;

    protected function setUp(): void
    {
        $this->httpClientMock = new HttpClientMock();
        $this->loggerMock = new LoggerMock();
        $this->pageModifiedDataBackendSender = new PageModifiedDataBackendSender($this->baseUrl, $this->httpClientMock, $this->loggerMock);
    }

    public function testPostIsValid(): void
    {
        $expectedBodyAsArray = [['testBody']];
        $responseMock = new ResponseMock();
        $responseMock->outputStatusCode = 200;
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
        ], $this->httpClientMock->inputOptions);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start sending trivia modified data to backend.',
                    'context' => [
                        'postData' => $requestBody,
                    ],
                ],
                [
                    'message' => 'Trivia modified data is sent successfully.',
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
            $this->fail('CouldNotSendTriviaModifiedDataException was expected to be thrown.');
        } catch (CouldNotSendTriviaModifiedDataException $exception) {
            self::assertSame('Trivia modified data could not be sent: Trivia modified data could not be sent: Response code is not 200.', $exception->getMessage());
        }

        self::assertSame(PageModifiedDataBackendSender::METHOD, $this->httpClientMock->inputMethod);
        self::assertSame($this->baseUrl . PageModifiedDataBackendSender::URL, $this->httpClientMock->inputUrl);
        self::assertEquals([
            'json' => $requestBody,
        ], $this->httpClientMock->inputOptions);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start sending trivia modified data to backend.',
                    'context' => [
                        'postData' => $requestBody,
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Sending trivia modified data has no response code 200.',
                    'context' => [
                        'postData' => $requestBody,
                        'statusCode' => 500,
                    ],
                ],
                [
                    'message' => 'Failed to send trivia modified data.',
                    'context' => [
                        'postData' => $requestBody,
                        'exceptionMessage' => 'Trivia modified data could not be sent: Response code is not 200.',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
