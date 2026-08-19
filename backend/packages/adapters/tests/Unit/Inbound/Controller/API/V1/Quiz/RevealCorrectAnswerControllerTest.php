<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz\RevealCorrectAnswerController;
use CuriosityConference\Adapters\Tests\Unit\Mocks\HubMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Quiz\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(RevealCorrectAnswerController::class)]
#[UsesClass(Id::class)]
final class RevealCorrectAnswerControllerTest extends TestCase
{
    private HubMock $hubMock;
    private LoggerMock $loggerMock;
    private RevealCorrectAnswerController $revealCorrectAnswerController;

    protected function setUp(): void
    {
        $this->hubMock = new HubMock();
        $this->loggerMock = new LoggerMock();
        $this->revealCorrectAnswerController = new RevealCorrectAnswerController(
            $this->hubMock,
            $this->loggerMock,
        );
    }

    public function testRevealCorrectAnswerIsValid(): void
    {
        $this->hubMock->outputUpdate = 'test';
        $payload = [
            'quizRoomId' => 'test',
        ];
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        $response = $this->revealCorrectAnswerController->__invoke($request);

        self::assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        self::assertSame(\sprintf('quiz/room/%s/answer/reveal', $payload['quizRoomId']), $this->hubMock->inputUpdateGroup[0]->getTopics()[0]);
        self::assertSame('{"action":"reveal"}', $this->hubMock->inputUpdateGroup[0]->getData());
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testRevealCorrectAnswerWithInvalidQuizRoomId(): void
    {
        $this->hubMock->outputUpdate = 'test';
        $payload = [
            'quizRoomId' => null,
        ];
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        $response = $this->revealCorrectAnswerController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for /api/v1/quiz/room/answer/reveal is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "id" with error: "Value for "id" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
