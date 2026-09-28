<?php

declare(strict_types=1);

namespace App\Tests\Unit\Trivia;

use App\Tests\Unit\Trivia\Mocks\PageModifiedDataBackendSenderMock;
use App\Trivia\PageModifiedBackendSendHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PageModifiedBackendSendHandler::class)]
final class PageModifiedBackendSendHandlerTest extends TestCase
{
    private PageModifiedDataBackendSenderMock $pageModifiedDataBackendSenderMock;
    private PageModifiedBackendSendHandler $pageModifiedBackendSendHandler;

    protected function setUp(): void
    {
        $this->pageModifiedDataBackendSenderMock = new PageModifiedDataBackendSenderMock();
        $this->pageModifiedBackendSendHandler = new PageModifiedBackendSendHandler($this->pageModifiedDataBackendSenderMock);
    }

    public function testHandleIsValid(): void
    {
        $postData = [
            [
                'type' => 'quiz',
                '_id' => 'e65f8b03',
                'quizTitle' => 'TestTitle',
                'description' => 'testDescription',
                'questions' => [
                    [
                        'type' => 'question',
                        '_id' => '38360be1',
                        'question' => 'testQuestion',
                        'answers' => [
                            [
                                'type' => 'answer',
                                '_id' => 'bf0addef',
                                'answer' => '1',
                                'correct' => true,
                            ],
                            [
                                'type' => 'answer',
                                '_id' => '94bdfc43',
                                'answer' => '2',
                            ],
                            [
                                'type' => 'answer',
                                '_id' => 'ad7c434b',
                                'answer' => '3',
                            ],
                            [
                                'type' => 'answer',
                                '_id' => 'c58e3932',
                                'answer' => '4',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->pageModifiedBackendSendHandler->handle($postData);

        $expectedInputPostData = [
            [
                'title' => 'TestTitle',
                'questions' => [
                    [
                        'question' => 'testQuestion',
                        'answers' => [
                            [
                                'answer' => '1',
                                'correct' => true,
                            ],
                            [
                                'answer' => '2',
                                'correct' => false,
                            ],
                            [
                                'answer' => '3',
                                'correct' => false,
                            ],
                            [
                                'answer' => '4',
                                'correct' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        self::assertEquals($expectedInputPostData, $this->pageModifiedDataBackendSenderMock->inputPostData);
    }
}
