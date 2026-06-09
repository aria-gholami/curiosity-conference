<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz\ListHighscoreByRoomController;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks\ListHighscoreByRoomHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\HighscoreCollection;
use CuriosityConference\Domain\Quiz\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(ListHighscoreByRoomController::class)]
#[UsesClass(Id::class)]
final class ListHighscoreByRoomControllerTest extends TestCase
{
    private ListHighscoreByRoomHandlerMock $listHighscoreByRoomHandlerMock;
    private LoggerMock $loggerMock;
    private ListHighscoreByRoomController $listHighscoreByRoomController;

    protected function setUp(): void
    {
        $this->listHighscoreByRoomHandlerMock = new ListHighscoreByRoomHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->listHighscoreByRoomController = new ListHighscoreByRoomController($this->listHighscoreByRoomHandlerMock, $this->loggerMock);
    }

    public function testListIsValid(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $quizRoom = new QuizRoom('test', 'test', $quiz, null, $user, QuizRoom::STATUS_STARTED);
        $expectedHighscoreCollection = new HighscoreCollection(
            [
                new Highscore(
                    'test',
                    $quizRoom,
                    $user,
                    0,
                ),
                new Highscore(
                    'test2',
                    $quizRoom,
                    $user,
                    0,
                ),
                new Highscore(
                    'test3',
                    $quizRoom,
                    $user,
                    0,
                ),
            ],
        );
        $this->listHighscoreByRoomHandlerMock->outputHighscoreCollection = $expectedHighscoreCollection;
        $request = new Request();
        $request->attributes->set('quizRoomId', 'test');

        $jsonResponse = $this->listHighscoreByRoomController->__invoke($request);

        self::assertSame(Response::HTTP_OK, $jsonResponse->getStatusCode());
        $jsonResponseAsArray = \json_decode($jsonResponse->getContent(), true);
        self::assertCount(3, $jsonResponseAsArray['data']);
        foreach ($jsonResponseAsArray['data'] as $jsonResponseItem) {
            self::assertIsArray($jsonResponseItem);
            self::assertSame(['userName', 'score'], \array_keys($jsonResponseItem));
            self::assertSame('test', $jsonResponseItem['userName']);
            self::assertSame(0, $jsonResponseItem['score']);
        }
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testListWithEmptyStringException(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $quizRoom = new QuizRoom('test', 'test', $quiz, null, $user, QuizRoom::STATUS_STARTED);
        $expectedHighscoreCollection = new HighscoreCollection(
            [
                new Highscore(
                    'test',
                    $quizRoom,
                    $user,
                    0,
                ),
                new Highscore(
                    'test2',
                    $quizRoom,
                    $user,
                    0,
                ),
                new Highscore(
                    'test3',
                    $quizRoom,
                    $user,
                    0,
                ),
            ],
        );
        $this->listHighscoreByRoomHandlerMock->outputHighscoreCollection = $expectedHighscoreCollection;
        $request = new Request();
        $request->attributes->set('quizRoomId', '');

        $jsonResponse = $this->listHighscoreByRoomController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for GET to /api/v1/quiz/room/{quizRoomId}/highscores is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "id" with error: "Value for "id" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testListWithLoadEntityFailedException(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $quizRoom = new QuizRoom('test', 'test', $quiz, null, $user, QuizRoom::STATUS_STARTED);
        $expectedHighscoreCollection = new HighscoreCollection(
            [
                new Highscore(
                    'test',
                    $quizRoom,
                    $user,
                    0,
                ),
                new Highscore(
                    'test2',
                    $quizRoom,
                    $user,
                    0,
                ),
                new Highscore(
                    'test3',
                    $quizRoom,
                    $user,
                    0,
                ),
            ],
        );
        $this->listHighscoreByRoomHandlerMock->outputHighscoreCollection = $expectedHighscoreCollection;
        $this->listHighscoreByRoomHandlerMock->throwException = new LoadEntityFailedException('test');
        $request = new Request();
        $request->attributes->set('quizRoomId', 'test');

        $jsonResponse = $this->listHighscoreByRoomController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Highscores could not be loaded.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
