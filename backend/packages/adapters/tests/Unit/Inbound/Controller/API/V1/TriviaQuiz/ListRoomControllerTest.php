<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuiz\ListRoomController;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz\Mocks\ListRoomHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\QuizRoomCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(ListRoomController::class)]
final class ListRoomControllerTest extends TestCase
{
    private ListRoomHandlerMock $listRoomHandlerMock;
    private LoggerMock $loggerMock;
    private ListRoomController $listRoomController;

    protected function setUp(): void
    {
        $this->listRoomHandlerMock = new ListRoomHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->listRoomController = new ListRoomController(
            $this->listRoomHandlerMock,
            $this->loggerMock,
        );
    }

    public function testListRoomIsValid(): void
    {
        $quiz = new TriviaQuiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $quizRoom = new TriviaQuizRoom(
            'test',
            'test',
            $quiz,
            null,
            $user,
            TriviaQuizRoom::STATUS_STARTED,
        );
        $quizRoom->addTriviaParticipant($user);
        $quizRoom->addTriviaParticipant($user);
        $quizRoomGroup = [
            $quizRoom,
            $quizRoom,
            $quizRoom,
        ];
        $this->listRoomHandlerMock->outputQuizRoomCollection = new QuizRoomCollection($quizRoomGroup);

        $jsonResponse = $this->listRoomController->__invoke();

        self::assertSame(Response::HTTP_OK, $jsonResponse->getStatusCode());
        $jsonResponseAsArray = \json_decode($jsonResponse->getContent(), true);
        self::assertCount(3, $jsonResponseAsArray['data']);
        foreach ($jsonResponseAsArray['data'] as $jsonResponseItem) {
            self::assertIsArray($jsonResponseItem);
            self::assertSame(['id', 'title', 'quiz', 'host', 'status', 'participants'], \array_keys($jsonResponseItem));
            self::assertSame('test', $jsonResponseItem['quiz']['title']);
            self::assertSame('test', $jsonResponseItem['host']['userName']);
            self::assertSame('test', $jsonResponseItem['host']['id']);
            self::assertSame(TriviaQuizRoom::STATUS_STARTED, $jsonResponseItem['status']);
            self::assertSame('test', $jsonResponseItem['participants'][0]['userName']);
            self::assertSame('test', $jsonResponseItem['participants'][0]['userId']);
        }
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testListRoomWithLoadEntityFailedException(): void
    {
        $this->listRoomHandlerMock->throwException = new LoadEntityFailedException('test');
        $quiz = new TriviaQuiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $quizRoomGroup = [
            new TriviaQuizRoom(
                'test',
                'test',
                $quiz,
                null,
                $user,
                TriviaQuizRoom::STATUS_STARTED,
            ),
            new TriviaQuizRoom(
                'test',
                'test',
                $quiz,
                null,
                $user,
                TriviaQuizRoom::STATUS_STARTED,
            ),
            new TriviaQuizRoom(
                'test',
                'test',
                $quiz,
                null,
                $user,
                TriviaQuizRoom::STATUS_STARTED,
            ),
        ];
        $this->listRoomHandlerMock->outputQuizRoomCollection = new QuizRoomCollection($quizRoomGroup);

        $jsonResponse = $this->listRoomController->__invoke();

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Quiz rooms could not be loaded.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
