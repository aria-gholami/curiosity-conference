<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRoomRepository;
use CuriosityConference\Adapters\Outbound\Quiz\QuizRoomUpdater;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Id;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(QuizRoomUpdater::class)]
#[UsesClass(Id::class)]
final class QuizRoomUpdaterTest extends TestCase
{
    private MockObject $quizRoomEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private QuizRoomUpdater $quizRoomUpdater;

    protected function setUp(): void
    {
        $this->quizRoomEntityRepositoryMock = $this->createMock(QuizRoomRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->quizRoomUpdater = new QuizRoomUpdater($this->quizRoomEntityRepositoryMock, $this->loggerMock);
    }

    public function testUpdateIsValid(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $question = new Question(
            '1',
            'test1',
            $quiz,
            new ArrayCollection(),
        );
        $quizRoomId = new Id('test');
        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('update')
            ->with($quizRoomId, $question, QuizRoom::STATUS_STARTED);
        $this->quizRoomUpdater->update($quizRoomId, $question, QuizRoom::STATUS_STARTED);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start updating status.',
                    'context' => [
                        'quizRoomId' => 'test',
                        'status' => QuizRoom::STATUS_STARTED,
                    ],
                ],
                [
                    'message' => 'Updating status was successful.',
                    'context' => [
                        'quizRoomId' => 'test',
                        'status' => QuizRoom::STATUS_STARTED,
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testUpdateWithUpdateFailedException(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $question = new Question(
            '1',
            'test1',
            $quiz,
            new ArrayCollection(),
        );
        $quizRoomId = new Id('test');
        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('update')
            ->willThrowException(new UpdateFailedException('test'));
        try {
            $this->quizRoomUpdater->update($quizRoomId, $question, QuizRoom::STATUS_STARTED);
            $this->fail('UpdateFailedException was expected to be thrown.');
        } catch (UpdateFailedException $exception) {
            self::assertSame('Update entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start updating status.',
                    'context' => [
                        'quizRoomId' => 'test',
                        'status' => QuizRoom::STATUS_STARTED,
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Updating status was not successful.',
                    'context' => [
                        'quizRoomId' => 'test',
                        'status' => QuizRoom::STATUS_STARTED,
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Updating status was not successful.',
                    'context' => [
                        'quizRoomId' => 'test',
                        'status' => QuizRoom::STATUS_STARTED,
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
