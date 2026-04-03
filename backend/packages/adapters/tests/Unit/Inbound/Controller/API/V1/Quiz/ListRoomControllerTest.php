<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz\ListRoomController;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks\ListRoomHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\QuizRoomCollection;
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
        $quizRoom = new QuizRoom(
            'test',
            $quiz,
            null,
            $user,
            QuizRoom::STATUS_STARTED,
        );
        $quizRoom->addParticipant($user);
        $quizRoom->addParticipant($user);
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
            self::assertSame(['id', 'quizTitle', 'quizDescription', 'quizCategory', 'hostUserName', 'participants'], \array_keys($jsonResponseItem));
            self::assertSame('test', $jsonResponseItem['quizTitle']);
            self::assertSame('test', $jsonResponseItem['quizDescription']);
            self::assertSame('test', $jsonResponseItem['quizCategory']);
            self::assertSame('test', $jsonResponseItem['hostUserName']);
            self::assertSame('test', $jsonResponseItem['participants'][0]['userName']);
        }
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testListRoomWithLoadEntityFailedException(): void
    {
        $this->listRoomHandlerMock->throwException = new LoadEntityFailedException('test');
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
        $quizRoomGroup = [
            new QuizRoom(
                'test',
                $quiz,
                null,
                $user,
                QuizRoom::STATUS_STARTED,
            ),
            new QuizRoom(
                'test',
                $quiz,
                null,
                $user,
                QuizRoom::STATUS_STARTED,
            ),
            new QuizRoom(
                'test',
                $quiz,
                null,
                $user,
                QuizRoom::STATUS_STARTED,
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
