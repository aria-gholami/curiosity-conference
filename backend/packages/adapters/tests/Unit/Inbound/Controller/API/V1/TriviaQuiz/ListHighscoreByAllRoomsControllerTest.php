<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuiz\ListHighscoreByAllRoomsController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz\Mocks\ListHighscoreByAllRoomsHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\AllRoomsHighscoreCollection;
use CuriosityConference\Domain\TriviaQuiz\QuizRoomHighscore;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(ListHighscoreByAllRoomsController::class)]
final class ListHighscoreByAllRoomsControllerTest extends TestCase
{
    private ListHighscoreByAllRoomsHandlerMock $listHighscoreByAllRoomsHandlerMock;
    private LoggerMock $loggerMock;
    private ListHighscoreByAllRoomsController $listHighscoreByAllRoomsController;

    protected function setUp(): void
    {
        $this->listHighscoreByAllRoomsHandlerMock = new ListHighscoreByAllRoomsHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->listHighscoreByAllRoomsController = new ListHighscoreByAllRoomsController($this->listHighscoreByAllRoomsHandlerMock, $this->loggerMock);
    }

    public function testListHighscoreByAllRoomsIsValid(): void
    {
        $quizRoomHighscoreGroup = [
            new QuizRoomHighscore('test', 1),
            new QuizRoomHighscore('test', 1),
            new QuizRoomHighscore('test', 1),
        ];
        $allRoomsHighscoreCollection = new AllRoomsHighscoreCollection($quizRoomHighscoreGroup);
        $this->listHighscoreByAllRoomsHandlerMock->outputAllRoomsHighscoreCollection = $allRoomsHighscoreCollection;

        $jsonResponse = $this->listHighscoreByAllRoomsController->__invoke();

        self::assertSame(Response::HTTP_OK, $jsonResponse->getStatusCode());
        $jsonResponseAsArray = \json_decode($jsonResponse->getContent(), true);
        self::assertCount(3, $jsonResponseAsArray['data']);
        foreach ($jsonResponseAsArray['data'] as $jsonResponseItem) {
            self::assertIsArray($jsonResponseItem);
            self::assertSame(['quizRoomTitle', 'quizRoomScore'], \array_keys($jsonResponseItem));
            self::assertSame('test', $jsonResponseItem['quizRoomTitle']);
            self::assertSame(1, $jsonResponseItem['quizRoomScore']);
        }
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testListHighscoreByAllRoomsWithLoadEntityFailedException(): void
    {
        $quizRoomHighscoreGroup = [
            new QuizRoomHighscore('test', 1),
            new QuizRoomHighscore('test', 1),
            new QuizRoomHighscore('test', 1),
        ];
        $allRoomsHighscoreCollection = new AllRoomsHighscoreCollection($quizRoomHighscoreGroup);
        $this->listHighscoreByAllRoomsHandlerMock->outputAllRoomsHighscoreCollection = $allRoomsHighscoreCollection;
        $this->listHighscoreByAllRoomsHandlerMock->throwException = new LoadEntityFailedException('test');

        $jsonResponse = $this->listHighscoreByAllRoomsController->__invoke();

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Highscores by all rooms could not be loaded.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
